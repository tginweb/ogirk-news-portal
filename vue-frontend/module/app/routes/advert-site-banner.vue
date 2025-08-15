<template>
  <q-page class="q-mt-lg q-mb-xl">

    <CLayout>

      <template v-slot:header>
        <el-page-header :showSubmenu="true" title="Реклама на сайте"/>
      </template>

      <template v-slot:default>


        <div class="q-mb-lg" v-if="true">

          <div class="row q-col-gutter-xl q-mb-xl">

            <div class="col-17" id="cleft">

              <div class="q-mb-lg" v-if="false">
                <a class="s-link-button text-accent"
                   href="https://drive.google.com/file/d/1cWzDkd_AnZDFM4iZGY85vWE_nBQab8md/view" target="_blank">скачать
                  Медиа-кит</a>
              </div>

              <q-markup-table
                bordered
                class="c-table s-font-lg"
                flat
                separator="cell"
                v-hc-sticky="{
                  stickTo: '#cleft',
                  top: 40,
                  followScroll: false,

                }"
              >
                <thead>
                <tr>
                  <th rowspan="2">Код зоны</th>
                  <th rowspan="2">Наименование</th>
                  <th rowspan="2">Размер</th>
                  <th colspan="4">Цены</th>
                </tr>
                <tr>
                  <th>Главная страница</th>
                  <th>Внутренние страницы</th>
                  <th>Главная + Внутренние</th>
                </tr>
                </thead>
                <tbody>

                <tr
                  :class="{
                    'selected' : selectedZone === zone
                  }"
                  :key="zone._id"
                  @click="onZoneSelect(zone)"
                  class="s-cursor-pointer"
                  v-for="zone of zones"
                >
                  <td class="text-center">
                    <b>Б{{zone.slug}}</b>
                  </td>
                  <td>
                    <b>{{zone.name}}</b>
                  </td>
                  <td>
                    {{zone.meta.zone_size}}
                  </td>
                  <td class="text-center">
                    {{zone.meta.zone_price_front | price(' ')}}
                  </td>
                  <td class="text-center">
                    {{zone.meta.zone_price_internal | price(' ')}}
                  </td>
                  <td class="text-center">
                    {{zone.meta.zone_price_all | price(' ')}}
                  </td>
                </tr>

                </tbody>
              </q-markup-table>

            </div>

            <div class="col-7 text-center">

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
                  height: view.style.wrapperHeight
                }"
                class="c-view"
                style="overflow:hidden;width:340px;"
              >

                <iframe
                  :src="view.url"
                  :style="{
                     width: view.style.width,
                     height: view.style.height,
                     transform: 'scale('+view.style.scale+')'
                  }"
                  @leoad="onIrameLoad(view)"
                  ref="view"
                  style="transform-origin: left top;overflow-y:scroll;"
                />


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
  import MAdvertable from "~module/ad/mixin/advertable";

  ;
  const queryString = require('query-string');

  export default {
    name: `page.advert.site.banner`,
    extends: CPage,
    components: {
      CLayout
    },
    mixins: [MAdvertable],
    props: {},
    apollo: {},
    data() {
      return {
        page: {
          title: 'Реклама на сайте'
        },
        selectedZone: null,

        mode: 'full',
        demoPage: '/',

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
        return [...this.$store.state.ad.adZones].filter(item => !item.meta.zone_private).sort((a, b) => (a.slug > b.slug) ? 1 : -1)
      },

      view() {
        const res = {}

        res.url = this.demoPage + '?ads_demo=1'

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
      onZoneSelect(zone) {
        this.selectedZone = zone
      },

      makeViewUrl(view) {
        let url = view.url + '?ads_demo=1'
        return url
      },

      onIrameLoad(view, index) {

        return;

      },


    },
    watch: {
      'selectedZone'(zone) {
        if  (!this.$refs['view'] || !this.$refs['view'].contentWindow || !this.$refs['view'].contentWindow.vstore) return;

        const cw = this.$refs['view'].contentWindow
        cw.vstore.dispatch('ad/adSelectDemoBannerZone', zone.slug)
      },
    },

    mounted() {


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
    padding: 5px;
    border: 1px solid #CCC;

    iframe {
      border: 0;
      padding: 0;
      margin: 0;
    }
  }

  .c-toggle {
    border: 1px solid #DDD;
  }

</style>
