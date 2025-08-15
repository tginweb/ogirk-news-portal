<template>

  <div class="com flex no-wrap">

    <div class="container" ref="menu">

      <div
        :id="'menu-item-' + index"
        style="display:inline-block"
        v-for="(item, index) of itemsLimited"
      >
        <component
          :is="item.native ? 'a':'span'"
          :href="item.native ? item.url : null"
          style="text-decoration: none"
        >

          <q-btn
            :class="{'is-active': opened === item || item.url === $route.path}"
            :key="index"
            :label="item.title"
            @click="onItemClose"
            @mouseleave="onItemLeave(item)"
            @mouseover="onItemOver(item)"
            class="c-nav-item text-weight-bold"
            color="primary"
            flat
            v-bind="bindItem(item)"
          >

            <transition name="fade">
              <div
                @mouseleave="opened=null"
                class="c-dropdown"
                style=""
                v-if="item.dropdown && opened === item && item.children"
              >
                <component
                  is="submenu"
                  :node="item"
                  @close="opened=null"
                  no-focus
                ></component>
              </div>
            </transition>
            <q-icon name="fas fa-angle-down q-ml-sm" size="12px" v-if="item.children && item.children.length"/>

          </q-btn>

        </component>


      </div>

      <q-btn-dropdown
        class="c-nav-item"
        color="primary"
        dense
        flat
        label="Еще"
        v-if="dropdownMenu.length"
      >
        <q-list>

          <q-item :key="index" clickable v-close-popup :to="item.url" v-for="(item, index) of dropdownMenu">
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
      }
    },
    computed: {

      menuLimit() {
        if (this.screen.gt.xl) return 100
        else if (this.screen.gt.lg) return 9
        else if (this.screen.gt.md) return 8
        else if (this.screen.gt.sm) return 7
      },

      itemsLimited() {
        return this.menu.slice(0, this.menuLimit)
      },

      dropdownMenu() {
        return this.menu.slice(this.menuLimit)
      },

    },
    methods: {
      bindItem(item) {
        const res = {}

        if (item.iconImage)
          res.icon = 'img:' + item.iconImage

        if (!item.native) {
          res.to = item.url
        }

        return res
      },

      onItemOver(item) {

        if (this.openedTimeout) clearTimeout(this.openedTimeout)

        this.openedTimeout = setTimeout(() => {
          this.opened = item
        }, 200)

      },
      onItemLeave() {
        if (this.openedTimeout) clearTimeout(this.openedTimeout)
      },
      onItemClose() {
        if (this.openedTimeout) clearTimeout(this.openedTimeout)
        this.opened = null
      },

      onDropdownLeave() {
        this.opened = null
      },
    },

  }
</script>

<style lang="scss" scoped>

  .com {

  }

  .c-nav-item {
    font-size: 20px;
    border-radius: 0;

    &.is-active {

    }

    /deep/ {
      .q-btn__wrapper {
        padding: 4px 11px;
      }
      img.q-icon {
        height: auto;
        max-width: 20px;
      }
    }

  }

  .c-dropdown {
    margin-top: 2px;
    box-shadow: 0 1px 5px rgba(0, 0, 0, 0.2), 0 2px 2px rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.12);
    background: #fff;
    position: absolute;
    left: 0;
    top: 100%;
    background: #fff;
    z-index: 100;
  }

</style>
