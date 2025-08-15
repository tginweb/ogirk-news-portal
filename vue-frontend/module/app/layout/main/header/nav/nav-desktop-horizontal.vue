<template>

  <div class="com" ref="com">

    <q-resize-observer @resize="onResize"/>

    <div class="flex no-wrap" ref="menu" >

      <div
        :id="'menu-item-' + index"
        :ref="'mi-'+index"
        style="display:inline-block"
        v-for="(item, index) of itemsLimited"
      >
        <component
          :href="item.native ? item.url : null"
          :is="item.native ? 'a':'span'"
          style="text-decoration: none"
        >

          <q-btn
            :class="{'--active': opened === item || item.url === $route.path}"
            :key="index"
            @click="onItemClick(item)"
            @mouseleave="onItemLeave(item)"
            @mouseover="onItemOver(item)"
            class="nav-item text-weight-bold"
            color="primary"
            flat
            v-bind="bindItem(item)"
          >
            <div class="flex no-wrap" style="white-space: nowrap;">

              <div>{{item.title}}</div>

              <q-icon :name="$icons.fasAngleDown" class="q-ml-sm q-my-auto" size="12px"
                      v-if="item.children && item.children.length"/>
            </div>

            <transition name="fade">
              <div
                @mouseleave="opened=null"
                class="dropdown --normal"
                v-if="item.dropdown && !item.dropdown.block && opened === item && item.children"
              >
                <component
                  :is="item.dropdown.is"
                  :node="item"
                  @close="opened=null"
                  no-focus
                ></component>
              </div>
            </transition>

          </q-btn>

        </component>


        <transition name="fade">
          <div

            @mouseover="onDropdownOver(item)"
            class="dropdown --block"
            v-if="item.dropdown && item.dropdown.block && opened === item && item.children"
          >
            <component
              :is="item.dropdown.is"
              :node="item"
              :params="item.dropdown"
              no-focus
            ></component>
          </div>
        </transition>

      </div>

      <q-btn-dropdown
        class="nav-item"
        color="primary"
        dense
        flat
        label="Еще"
        v-if="dropdownMenu.length"
      >
        <q-list>

          <q-item :key="index" :to="item.url" clickable v-close-popup v-for="(item, index) of dropdownMenu">
            <q-item-section>
              <q-item-label>{{item.title}}</q-item-label>
            </q-item-section>
          </q-item>

        </q-list>
      </q-btn-dropdown>

    </div>

  </div>

</template>

<script>

  import Submenu from './submenu/submenu'
  import SubmenuCat from './submenu/submenu-cat'

  export default {
    components: {
      Submenu,
      SubmenuCat,

    },
    props: {
      menu: {},
    },
    data() {
      return {
        opened: null,
        openedTimeout: null,
        active: null,
        isMounted: false,
        menuLimit: 100
      }
    },
    computed: {

      itemsLimited() {
        return this.menu.slice(0, this.menuLimit)
      },

      dropdownMenu() {
        return this.menu.slice(this.menuLimit)
      },

    },
    mounted() {

      this.menuLimitCalc();

      //  this.isMounted = true
    },
    methods: {

      onResize() {
        this.menuLimitCalc();
      },

      menuLimitCalc() {

        this.menuLimit = 100

        this.$nextTick(() => {

          const maxWidth = this.$refs.com.clientWidth - 50

          let usedWidth = 0, itemsLimit = 0

          this.itemsLimited.forEach((item, index) => {

            usedWidth = usedWidth + this.$refs['mi-' + index][0].clientWidth

            if (usedWidth <= maxWidth) {
              itemsLimit++;
            }
          })

          this.menuLimit = itemsLimit;
        })
      },

      bindItem(item) {
        const res = {}

        if (item.iconImage)
          res.icon = 'img:' + item.iconImage

        if (!item.native) {
          res.to = item.url
        }

        return res
      },
      itemHorverClearTimeout() {
        if (this.openedTimeout) clearTimeout(this.openedTimeout)
      },
      onItemOver(item) {
        this.itemHorverClearTimeout();

        this.openedTimeout = setTimeout(() => {
          this.opened = item
        }, this.opened ? 300 : 400)
      },
      onItemLeave() {
        this.itemHorverClearTimeout();
        this.openedTimeout = setTimeout(() => {
          this.opened = null
        }, 200)
      },
      onItemClick(item) {
        if (item.url) {
          this.onItemClose()
        } else {
          this.itemHorverClearTimeout();
          this.opened = item
        }
      },
      onItemClose() {
        this.itemHorverClearTimeout();
        this.opened = null
      },
      onDropdownLeave() {
        this.opened = null
      },
      onDropdownOver(item) {
        this.itemHorverClearTimeout();
        this.opened = item
      },
    },

  }
</script>

<style lang="scss" scoped>

  .com {

  }

  .nav-item {
    font-size: 16px;
    border-radius: 0;

    &.--active {

    }

    /deep/ {
      .q-btn__wrapper {
        padding: 4px 9px;
      }

      img.q-icon {
        height: auto;
        max-width: 20px;
      }
    }
  }

  .dropdown {
    margin-top: 2px;
    box-shadow: 0 1px 5px rgba(0, 0, 0, 0.2), 0 2px 2px rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.12);
    background: #fff;
    position: absolute;
    left: 0;
    top: 100%;
    background: #fff;
    z-index: 100;

    &.--block {
      width: 100%;
    }
  }

</style>
