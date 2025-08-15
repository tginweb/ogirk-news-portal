<template>

  <div class="com" ref="com">

    <a href="/itogi-2024" target="_blank" style="display: block;line-height: 0;font-size: 0;" class="" >
      <img style="width: 100%;" src="/statics/itogi2024psd.jpg"/>
    </a>

    <div class="header">

      <div class="header__top">

        <div class="container">

          <div class="row q-col-gutter-sm-lg q-col-gutter-xl-lg items-center">

            <div class="col-5 col-md-5 col-lg-5 col-xl-4">

              <router-link class="logo" to="/">
                <img src="/statics/hds-logo.png">
              </router-link>

            </div>

            <div class="col-14 col-md-12 col-lg-13 col-xl-13">

              <div class="row items-center q-col-gutter-md-lg q-col-gutter-y-xs">

                <div class="col-24 col-md-auto">
                  <div class="menu q-gutter-y-sm q-gutter-xs-x-md q-gutter-lg-x-md s-font-xs">

                    <template v-if="queries.issues.result">
                      <router-link
                        :key="node.nid"
                        to="/issue/og"
                        class="menu__item s-link-wrapper"
                        v-for="node of queries.issues.result.nodes"
                      >
                        <q-icon :name="$icons.farNewspaper" class="s-link-icon  q-mr-sm"/>
                        <span class="s-link">Выпуск от {{$util.date.timestampToFormat(node.created, 'DD MMMM YYYY')}}</span>
                      </router-link>
                    </template>

                    <router-link class="menu__item s-link-wrapper" to="/page/info-about">
                      <q-icon :name="$icons.fasInfoCircle" class="s-link-icon q-mr-sm"/>
                      <span class="s-link">Издание</span>
                    </router-link>

                    <a class="menu__item s-link-wrapper s-cursor-pointer" href="kkk" v-if="false">
                      <q-icon name="fas fa-glasses" class="s-link-icon q-mr-sm"/>
                      <span class="s-link">Для слабовидящих</span>
                    </a>

                  </div>
                </div>

                <div class="col-24 col-md-auto s-align-center s-align-md-left">

                  <div class=" icons inline-block q-gutter-md ">

                    <a
                      :href="item.url"
                      :key="index"
                      class="icons__item"
                      target="_blank"
                      v-for="(item, index) of socials"
                      :style="{backgroundColor: item.color}"
                    >
                      <q-icon :name="item.icon" color="white"  size="18px"/>
                    </a>

                  </div>

                </div>

              </div>

            </div>


            <div class="col-5 col-md-7 col-lg-6 col-xl-7 text-right">

              <a class="logo-pravo s-t-d-none" href="https://www.ogirk.ru/pravo/reestr">
                <img src="/statics/hds-logo-pravo.png"/>
                <div class="s-font-4xs">
                  Официальный интернет-портал правовой информации Иркутской области
                </div>
              </a>

            </div>

          </div>

        </div>

      </div>

      <div
        class="header__sticky"
        v-hc-sticky="{
          stickTo: '#q-app',
          top: 0,
          onStart: onStickStart,
          onStop: onStickStop,
          followScroll: false,
        }"
      >
        <transition name="menu-up">

          <div
            class="header__main"
            v-show="stickyShow"
          >
            <div class="container">

              <div class="header__main__nav flex no-wrap items-center" style="">

                <CNav
                  :menu="$store.getters['app/menuItemsPrimary']"
                  style="width: calc(100% - 63px) !important;"
                  :itemDense="true"
                  :itemLinkStyle="{lineHeight: '1.50em'}"
                  :itemWrapperDense="true"
                  :moreDropdown="true"
                  :itemIconHide="true"
                  class="header__normal__catalog__nav s-font-sm full-width"
                  itemColor="primary"
                  itemIs="button"
                  itemLinkClass="q-px-sm q-py-sm no-underline text-weight-bold text-no-wrap"
                />

                <q-space/>

                <div>
                  <q-btn
                    @click="$store.dispatch('dialogShow', ['app/search'])"
                    color="primary"
                    dense
                    flat
                  >
                    <q-icon :name="$icons.matSearch" size="30px"/>
                  </q-btn>
                </div>

              </div>

            </div>
          </div>

        </transition>
      </div>

      <CHeadline v-if="!$route.meta.headlineHide"/>

    </div>

    <q-btn
      @click="onNavUp"
      class="scrollup"
      color="grey-8"
      :icon="$icons.fasAngleUp"
      label="наверх"
      outline
      v-if="scroll.position > 500 && !$store.state.app.dialog.player.value && !$store.state.app.dialog.stream.value"
    />

  </div>

</template>

<script>

  import CNav from '@common/ui/components/nav/nav'
  import CHeadline from './block/headline'

  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";
  import {fabOdnoklassniki, fabTelegramPlane} from "@quasar/extras/fontawesome-v5";
  import MAdvertable from '~module/ad/mixin/advertable'

  export default {
    name: 'layout.header.desktop',
    serverCacheKey1: props =>  'layout.header.desktop',
    mixins: [MAdvertable],
    components: {
      CNav,
      CHeadline,
    },
    apollo: {
      issues: generateQueryInfo('issues', require('~module/entity/graphql/getPosts.gql'), {}),
    },
    data() {
      return {
        isMounted: false,
        bannerSlide: 1,
        bannerSlides: [
          '/statics/banner/_1920х250-01.jpg',
          '/statics/banner/_1920х250-02.jpg',
          '/statics/banner/_1920х250-03.jpg',
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
        queries: {
          issues: {
            vars: {
              filter: {
                type: 'sm-issue-print',
              },
              nav: {
                limit: 1,
              },
              imageSize: 'c152x220',
              cache: 'TEMP_MD'
            },
            result: null
          },
        },
        socials: [
          {icon: this.$icons.fabVk, url: 'https://vk.com/ogirk', color: '#4D75A3'},
          {icon: this.$icons.fabYoutube, url: 'https://www.youtube.com/channel/UC5M9401ITV-nkfPMM75VUxQ?view_as=subscriber', color: '#FF0000'},
          {icon: this.$icons.fabOdnoklassniki, url: 'https://ok.ru/ogirk', color: '#F56040'},
          {icon: this.$icons.fabTelegramPlane, url: 'https://t.me/ogirk', color: '#0088cc'},
          {icon: 'img:/statics/icons/dzen.png', url: 'https://dzen.ru/id/5a8d27835f496759a80b6822', color: '#ffffff'},


        ]
      }
    },
    computed: {
      linkModeSpecial() {
       return 'http://special.og.loc' + this.$route.path
      },
      stickyShow() {
        return !this.sticky || (this.scroll.directionUp && this.scroll.inflexionDelta > this.stickyParams.showInflexionDelta) || this.scroll.position < this.stickyParams.hideScrollPosition
      }
    },
    mounted() {
      this.isMounted = true
    },
    methods: {

      onStickStart() {
        this.sticky = true
      },
      onStickStop() {
        this.sticky = false
      },
      onScroll(info) {
        this.scroll.directionUp = info.direction === 'up'
        this.scroll.position = info.position
        this.scroll.inflexionDelta = info.inflexionPosition - info.position
      },
      onNavUp() {
        this.$util.dom.scrollUp();
      },


    },

    created() {

      console.log('HEADER DESKTOP')
    }
  }
</script>

<style lang="scss" scoped>

  .com {

  }

  .header__top {
    background: $primary;
    margin-top: 0;
    margin-bottom: 0;
    padding: 10px 0 10px;
    color: #FFF;

    /deep/ a {
      color: #FFF;
    }
  }

  .header__sticky {
    z-index: 10;
    height: 41px;
  }

  .header__main {
    background: #fff;
    color: #000;
    border-bottom: 2px solid #BBB;
    overflow1: hidden;
    z-index: 100;
    position: relative;

    &.top-sticky {
      border-bottom: 2px solid #e9ebec;
    }
  }

  .header__main__nav {
    position: relative;
    z-index: 1;
  }

  .icons {
    .icons__item {
      display: inline-block;
      text-decoration: none;
      padding: 3px 4px;
      line-height: 1;
    }
  }

  .scrollup {
    position: fixed;
    z-index: 10;
    bottom: 30px;
    right: 30px;
  }

  .menu {
    .menu__item {
      display: inline-block;
    }
  }

  .logo {
    img {
      width: 208px;
    }
  }

  .logo-pravo {
    line-height: 0;
    display: inline-block;

    img {
      width1: 220px;
      max-width1: 100%;

      max-width: 300px;
    }

    div {
      line-height: 1.3;
      margin-top: -4px;
    }
  }

  .banner-slide {
    line-height:0;font-size:0;text-align:center;width:100vw;

    img {
      display: inline-block !important;
    }
  }
</style>
