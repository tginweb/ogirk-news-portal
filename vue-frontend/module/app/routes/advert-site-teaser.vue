<template>
  <q-page class="q-mt-lg q-mb-xl" ref="page">

    <CLayout>

      <template v-slot:header>
        <el-page-header :showSubmenu="true" title="Реклама на сайте"/>
      </template>

      <template v-slot:default>


        <div class="q-mb-lg" v-show="isMounted">

          <div class="row q-col-gutter-xl q-mb-xl">

            <div class="col-17" id="cleft">

              <div
                class="sm-content-style"
                ref="teaser-info-content"
                v-html="pageData.teaserPagePost.contentFormatted"
              >
              </div>

            </div>

            <div class="col-7 text-center" id="cright">

              <div
                ref="demo"
              >

                <q-btn-toggle
                  :options="modes"
                  class="c-toggle q-mb-sm"
                  color="white"
                  no-caps
                  rounded
                  text-color="primary"
                  toggle-color="primary"
                  unelevated
                  v-model="mode"
                />

                <q-option-group
                  :options="pages"
                  class="q-mb-sm"
                  inline
                  v-model="demoPage"
                />

                <div class="q-mb-sm">
                  <a :href="view.url" class="s-link text-accent" target="_blank">открыть пример размещения</a>
                </div>

                <div
                  :style="{
                  height1: view.style.wrapperHeight
                }"
                  class="c-view"
                  style="position:relative;"
                  v-show="!demoLoading"
                >

                  <iframe
                    :src="view.url"
                    :style="{
                     width: view.style.width,
                     height: view.style.height,
                     transform: 'scale('+view.style.scale+')'
                  }"
                    @load="onIrameLoad(view)"
                    ref="view"
                    style="transform-origin: left top;overflow-y:scroll;"
                  />

                </div>

                <div class="q-pt-lg" v-if="demoLoading">
                  <q-spinner
                    :thickness="2"
                    color="primary"
                    size="3em"
                  />
                  загрузка примера
                </div>

              </div>


            </div>

          </div>

        </div>

      </template>

    </CLayout>

  </q-page>
</template>

<script>
  import CPage from "~module/app/component/route";
  import CLayout from '~module/app/layout/main/page/1cols'
  import setPageRouteData from "@common/core/lib/route/set-page-data";
  import MAdvertable from "~module/ad/mixin/advertable";
  import MContentHandlerable from "@common/core/mixin/content-handlerable";

  const queryString = require('query-string');

  const loadPageData = async (pageData, ctx) => {

    try {

      let {data} = await ctx.apolloClient.query({
        query: require('~module/entity/graphql/getPost.gql'),
        fetchPolicy: 'no-cache',
        variables: {
          filter: {
            slug: 'site-teaser',
          },
          content: true,
          view: 'public_full'
        },
      })

      pageData.teaserPagePost = data.res

    } catch (e) {

      console.log(e)
    }

    return pageData;
  }

  export default {
    name: `page.advert.site.teaser`,
    extends: CPage,
    components: {
      CLayout
    },
    mixins: [MAdvertable, MContentHandlerable],
    props: {},
    apollo: {},
    async preFetch(ctx) {
      return setPageRouteData('static', ctx, loadPageData)
    },
    data() {
      return {
        isMounted: false,
        page: {
          title: 'Тизерная реклама'
        },

        selectedZone: null,

        mode: 'full',

        demoPage: '/',
        demoLoading: true,

        modes: [
          {
            label: 'Полная версия',
            value: 'full',
          },
          {
            label: 'Мобильная',
            value: 'mobile',
          },
        ],

        pages: [
          {
            label: 'Главная',
            value: '/',
          },
          {
            label: 'Статья',
            value: '/2020/8/13/fotografii-irkutjan-frontovikov-sobirajut-dlja-izdanija-k-jubileju-pobedy',
          },
          {
            label: 'Рубрика',
            value: '/category/social',
          },
        ],

        views: []
      }
    },

    computed: {
      zones() {
        return [...this.$store.state.ad.adTeaserZones]
      },

      view() {
        const res = {}

        res.url = this.demoPage + '?ads_teaser_demo=1'

        if (this.mode === 'full') {
          res.style = {
            width: '1260px',
            height: '5000px',
            scale: '0.26',
            wrapperHeight: '1200px'
          }
        } else {
          res.style = {
            width: '100%',
            height: '3000px',
            scale: '1',
            wrapperHeight: '600px'
          }
        }

        return res
      },

    },
    methods: {


      onIrameLoad(view, index) {
        this.demoLoading = false
        return;
      },

    },
    watch: {
      'selectedZone'(zone) {
        const cw = this.$refs['view'].contentWindow
        cw.vstore.dispatch('ad/adSelectDemoTeaserZone', zone)
      },
      'view.url'() {
        this.demoLoading = true
      }
    },

    beforeDestroy() {
      this.detachContentHandlers()
    },

    mounted() {

      const handlers = this.$registry.applyHooks('content/handlers')

      handlers['zone-clock'] = {
        selector: 'a[href^="#zone"]',
        event: 'click',

        callback: (el) => {
          if  (!this.$refs['view'] || !this.$refs['view'].contentWindow || !this.$refs['view'].contentWindow.vstore) return;

          const query = queryString.parse(el.getAttribute('href').substring(1))
          this.selectedZone = query.zone
          this.$util.dom.scrollTo({el: this.$refs.page.$el, duration: true})
        },
      }

      this.attachContentHandlers(this.$refs['teaser-info-content'],  Object.values(handlers))

      this.isMounted = true
    }
  }
</script>
<style lang="scss" scoped>

  .c-table {
    tbody td,
    thead th {
      font-size: 16px;
      white-space: normal;
    }

    tr {
      &.selected {
        background-color: #ff8f00;
        color: #FFF;
      }
    }
  }

  .c-view {
    overflow: auto;
    width: 340px;

    iframe {
      padding: 10px;
      margin: 0;
      height1: 20000px !important;
      border: 3px solid #999;
    }

  }

  .c-toggle {
    border: 1px solid #DDD;
  }

  /deep/ table {
    height: auto !important;

    td {
      height: auto !important;
      width: auto !important;
    }

    tr {
      height: auto !important;
      width: auto !important;
    }
  }

</style>
