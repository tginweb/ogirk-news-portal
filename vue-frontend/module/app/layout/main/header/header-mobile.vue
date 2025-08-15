<template>

  <div>

    <q-scroll-observer @scroll="onScroll"/>

    <a href="itogi-2024" target="_blank" style="display: block;line-height: 0;font-size: 0;" class="" >
      <img style="width: 100%;" src="/statics/itogi2024psd.jpg"/>
    </a>

    <div class="header">

      <div class="header__top flex items-center no-wrap bg-primary text-white q-px-sm  q-py-xs">

        <div style="margin-left: 4px;">

          <q-btn
            v-if="isOverlay"
            class="q-mr-lg"
            dense
            flat
            icon="fas fa-arrow-left"
            round
            size="11px"
            @click="onBack"
          >
            <span class="q-ml-sm" style="font-size:14px;">назад</span>
          </q-btn>

        </div>

        <div class=" q-mr-lg">

          <div class="text-center">
            <router-link to="/">
              <img src="/statics/hds-logo.png" style="max-height:24px;">
            </router-link>
          </div>

        </div>

        <div class=" q-mr-auto">

          <template v-if="queries.issues.result">
            <router-link
              v-for="node of queries.issues.result.nodes"
              :key="node.nid"
              class="s-font-xxs s-t-d-none text-white"
              to="/issue/og"
            >
              <span class="s-link">Номер {{ $util.date.timestampToFormat(node.created, 'DD.MM.YYYY') }}</span>
            </router-link>
          </template>

        </div>

        <div>
          <q-btn
            dense
            flat
            :icon="$icons.matSearch"
            round
            size="13px"
            @click="$store.dispatch('dialogShow', ['app/search'])"
          />
        </div>

      </div>


      <div
        v-hc-sticky="{
          stickTo: '#q-app',
          top: 0,
          onStart: onStickStart,
          onStop: onStickStop,
          followScroll: false,
          timeout: 150,
          updateDisable1: true
        }"
        :style="{
          height: withSubnav ? '71px' : '30px'
        }"
        class="header__sticky"
      >

        <transition name="menu-up-mobile">

          <div
            v-show="stickyShow"
            class="header__wrap text-white bg-primary"
          >


            <div class="header__nav" style="position: relative">

              <div style="position: absolute; left: 6px; top: 2px;">
                <q-btn
                  dense
                  flat
                  icon="menu"
                  round
                  size="12px"
                  @click="drawer = !drawer"
                />
              </div>

              <q-tabs
                v-model="tabPrimary"
                align="justify"
                class="nav-primary"
                dense
                indicator-color="white"
                indicator-color1="white"
                style="margin-left:37px;"
              >
                <template v-for="item of menuMobile">
                  <q-route-tab
                    v-if="item.url.indexOf('http') !== 0"
                    :key="item.url"
                    :label="item.title"
                    :name="item.url"
                    :to="item.url"
                    class="nav-primary__item"
                    content-class="q-pt-none "
                    exact
                  />
                  <q-tab
                    v-else
                    :key="item.url"
                    :label="item.title"
                    :name="item.url"
                    class="nav-primary__item"
                    content-class="q-pt-none "
                    @click="onNav(item)"
                  />
                </template>
              </q-tabs>

            </div>

            <q-tabs
              v-if="withSubnav"
              align="justify"
              class="nav-secondary text-white bg-primary"
              dense
              indicator-color="transparent"
              narrow-indicator
              style="padding-left:4px;"
            >
              <q-route-tab
                v-for="item of primaryItemChildren"
                :key="item.url"
                :label="item.title"
                :name="item.url"
                :to="item.url"
                class="nav-secondary__item q-px-none"
              />
            </q-tabs>

          </div>

        </transition>

      </div>


    </div>

    <CHeadline
      v-if="$route.meta.headlineHide!==true && $route.meta.headlineHide!=='mobile'"
    />

    <q-drawer
      v-model="drawer"
      :breakpoint="500"
      :width="200"
      bordered
      class=""
      content-class="bg-grey-8"
      overlay
    >
      <q-scroll-area class="fit">
        <CDrawer
          :menu="menuDesktop"
        ></CDrawer>
      </q-scroll-area>
    </q-drawer>

  </div>


</template>

<script>

import CDrawer from './nav/nav-mobile-drawer'
import CHeadline from "~module/app/layout/main/header/block/headline";
import {openURL} from 'quasar'
import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";

const findMenuPath = (menu, url) => {

  const scanTree = (items, parents) => {

    let foundPath;

    for (let i = 0; i < items.length; i++) {
      const item = items[i]
      if (item.url == url) {
        return [...parents, ...[item]]
      } else {
        if (item.children) {
          if (foundPath = scanTree(item.children, [...parents, ...[item]])) {
            return foundPath
          }
        }
      }
    }
  }

  return scanTree(menu, [])
}

export default {
  name: 'layout.header.mobile',

  apollo: {
    issues: generateQueryInfo('issues', require('~module/entity/graphql/getPosts.gql'), {}),
  },
  components: {
    CDrawer,
    CHeadline
  },
  data() {
    return {
      isMounted: false,
      bannerSlides: [
        '/statics/banner/m_1920х250-01.jpg',
        '/statics/banner/m_1920х250-02.jpg',
        '/statics/banner/m_1920х250-03.jpg',
      ],
      stickyParams: {
        showInflexionDelta: 170,
        hideScrollPosition: 300
      },
      sticky: false,
      scroll: {
        directionUp: null,
        threshold: 0,
        position: 0,
        inflexionDelta: 0,
      },
      drawer: false,
      tabPrimary: null,
      tabSecondary: null,

      queries: {
        issues: {
          vars: {
            filter: {
              type: 'sm-issue-print',
            },
            nav: {
              limit: 1,
            },
            imageSize: 'c152x220'
          },
          result: null
        },
      },
    }
  },
  mounted() {

    this.updateTabs()
    this.isMounted = true
  },
  computed: {

    withSubnav() {
      return this.primaryItemChildren && this.primaryItemChildren.length
    },

    isOverlay() {
      return this.$route.meta.overlay || this.$route.meta.mm
    },

    stickyShow() {
      return !this.sticky || (this.scroll.directionUp && this.scroll.inflexionDelta > this.stickyParams.showInflexionDelta) || this.scroll.position < this.stickyParams.hideScrollPosition
    },

    menuDesktop() {
      return this.$store.getters['app/menuItemsPrimary']
    },

    menuMobile() {
      return this.$store.getters['app/menuItemsPrimaryMobile']
    },

    menuPath() {
      return findMenuPath(this.menuMobile, this.$route.path)
    },

    primaryItem() {
      return this.tabPrimary && this.menuMobile.find(item => item.url === this.tabPrimary)
    },

    primaryItemChildren() {
      return this.primaryItem && this.primaryItem.children || []
    },

  },
  methods: {

    onBack() {
      if (this.$route.query.mm) {
        window.close()
      } else {
        this.$router.go(-1)
      }
    },

    onNav(item) {
      if (item.url.indexOf('http') === 0) {
        openURL(item.url)
      } else {
        this.$router.push(item.url)
      }
    },

    updateTabs() {
      let path = this.menuPath
      this.tabPrimary = path && path[0] ? path[0].url : null
      this.tabSecondary = path && path[1] ? path[1].url : null
    },

    onScroll(info) {
      this.scroll.directionUp = info.direction === 'up'
      this.scroll.position = info.position
      this.scroll.inflexionDelta = info.inflexionPosition - info.position
    },

    onStickStart() {
      this.sticky = true
    },

    onStickStop() {
      this.sticky = false
    },
  },
  watch: {

    '$route': {
      handler(route) {
        this.$nextTick(() => {
          setTimeout(() => {
            this.updateTabs()
          }, 10)
        })
      },
    }
  },

  created() {

    console.log('HEADER MOBILE')
  }
}
</script>

<style lang="scss" scoped>

.com {

}

.header__sticky {
  height1: 71px;
  z-index: 10;
}

.header__nav {
  position: relative
}

.nav-primary {
  /deep/ .q-tab {

    min-height: 32px;

    .q-focus-helper {
      display: none;
    }

    .q-tab__content {
      padding: 0;
      min-width: auto;
    }

    .q-tab__label {
      font-size: 14px;
      font-weight: 600;
    }

    .q-tab__indicator {
      height: 3px;
    }
  }

  .nav-primary__item {
    min-height: 30px;
    min-width: auto;
    padding: 2px 2px 2px 2px;
    margin: 1px 4px 0px 4px;
    outline: none;
  }
}


.nav-secondary {
  background: #FFF;
  border-bottom: 1px solid #CCC;


  .nav-secondary__item {
    margin-left: 9px;
    min-height: 34px;
    outline: none;
  }

  /deep/ .q-tab {
    text-transform: none;

    .q-focus-helper {
      display: none;
    }

    .q-tab__content {
      padding: 0;
    }

    .q-tab__label {
      font-size: 15px;
    }

    &.q-tab--active {
      .q-tab__label {
        font-weight: bold;
      }
    }
  }
}

.banner-item {
  display:block;
  height: 100px;
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center center;
}

.banner-slide {
  line-height:0;font-size:0;text-align:center;width:100vw;

  img {
    display: inline-block !important;
  }
}

</style>
