<template>

  <div class="com fit">

    <q-list class="nav --news bg-white">

      <template v-for="(item, index) of itemsPrimary">

        <q-expansion-item
          :content-inset-level="0.25"
          :key="index"
          :label="item.title"
          :to="item.url"
          v-if="item.children && item.children.length>0"
          class="nav__item"
          header-class="nav__item__header q-px-md q-py-sm"
        >
          <q-item
            :key="subindex"
            :to="subitem.url"
            clickable
            v-for="(subitem, subindex) of item.children"
            class="nav__subitem q-px-sm q-py-sm"
          >
            <q-item-section>
              <span>{{subitem.title}}</span>
            </q-item-section>
          </q-item>
        </q-expansion-item>

        <q-item
          :to="item.url"
          clickable
          v-else
          class="nav__item q-px-md q-py-sm"
        >
          <q-item-section class="nav__item__header ">
            {{item.title}}
          </q-item-section>
        </q-item>

      </template>

    </q-list>

    <q-list class="nav --other">

      <template v-for="(item, index) of itemsSecondary">

        <q-expansion-item
          :content-inset-level="0.25"
          :key="index"
          :label="item.title"
          :to="item.url"
          expand-separator
          v-if="item.children && item.children.length>0"
          class="nav__item"
          header-class="nav__item__header q-px-md q-py-sm"
        >
          <q-item
            :key="subindex"
            :to="subitem.url"
            clickable
            v-for="(subitem, subindex) of item.children"
            class="nav__subitem q-px-sm q-py-sm"
          >
            <q-item-section>
              {{subitem.title}}
            </q-item-section>
          </q-item>
        </q-expansion-item>

        <q-item
          :to="item.url"
          clickable
          v-else
          class="nav__item q-px-md q-py-sm"
        >
          <q-item-section class="nav__item__header ">
            {{item.title}}
          </q-item-section>
        </q-item>

      </template>

    </q-list>

  </div>

</template>

<script>

  import Submenu from './submenu/submenu'
  import SubmenuCat from './submenu/submenu-cat'

  export default {
    components: {
      Submenu,
      SubmenuCat
    },
    props: {
      menu: {},
    },
    methods: {

    },
    data() {
      return {

      }
    },
    computed: {
      itemsPrimary() {
        return this.menu.filter(item => item.entityNid)
      },
      itemsSecondary() {
        return this.menu.filter(item => !item.entityNid)
      },
    },
  }
</script>

<style lang="scss" scoped>


  .nav__item {
    /deep/ .nav__item__header {
      font-size: 15px;
      color: #006697;
      text-transform: uppercase;
      font-weight: 600;
      min-height: auto;
    }
  }

  .nav__subitem {
    color: #333;
    font-weight: 500;
    span {
      border-bottom: 1px dotted #777;
      width: fit-content;
    }
  }

  .nav.--other {

    /deep/ {
      .q-expansion-item__toggle-icon,
      .nav__item__header {
        color: #fff;
      }
    }

    .nav__subitem {
      color: #fff;

      span {
        border-bottom: 1px dotted #fff !important;
      }

    }
  }

</style>
