<template>
  <q-page :style-fn="pageStyle" class="q-mt-lg q-mb-xl">


    <div v-if="pageQueriesLoading">

      <div class="container flex" style="height: calc(100vh - 180px);">

        <el-spinner class="q-mx-auto q-my-auto"/>

      </div>

    </div>

    <div v-show="!pageQueriesLoading">

      <div class="container">

        <div class="row q-col-gutter-lg q-mb-lg">

          <div class="col-24 col-md-15 col-lg-17">

            <query-view
              :queryResult="queries.topGrid.result"
              :queryState="queries.topGrid.state"
              queryId="page.front.topGrid"
            >
              <template v-slot:default="{items, loading, qid}">

                <el-skeleton v-if="loading"/>

                <query-items-regional
                  :items="items"
                  :regions="[
                  {
                    limit: 1,
                    outerClass: {

                    },
                    item: {
                      queryId: qid,
                      is: 'query-item-1',
                      expandable: false,
                      class: 's-width-1-1 s-width-md-2-3 s-width-lg-2-3',
                      style: {
                        float: 'left',
                        paddingRight: '1px',
                        paddingBottom: '1px'
                      },
                      elements: {
                        title: {
                          defclass: 's-font-md s-font-xxs-lg s-font-md-xxl'
                        }
                      }
                    }
                  },
                  {
                    limit: isDesktop ? 5 : 4,
                    innerClass: {

                    },
                    item: {
                      queryId: qid,
                      is: 'query-item-1',
                      expandable: true,
                      class: 's-width-1-1 s-width-xs-1-2 s-width-sm-1-2 s-width-md-1-3 s-width-lg-1-3',
                      style: {
                        float: 'left',
                        paddingRight: '1px',
                        paddingBottom: '1px'
                      },
                      elements: {
                        title: {
                          defclass: 's-font-md'
                        }
                      }
                    }
                  },
                ]"
                  class="clearfix"
                  v-else
                />

              </template>

            </query-view>

            <ad-zone
              :ads="adsByZoneSlug[3]"
              :limit="2"
              class="q-pt-lg"
              zoneCode="3"
            >
              <template v-slot:default="{items}">
                <query-items-grid
                  :item="{
                   class: 'col-24'
                  }"
                  :items="items"
                  class="q-gutter-lg"
                  rowClass="q-col-gutter-y-lg"

                />
              </template>
            </ad-zone>

            <query-view
              :queryResult="queries.topLine.result"
              :queryState="queries.topLine.state"
              queryId="page.front.topLine"
              class="q-mt-lg"
            >
              <template v-slot:default="{items, state, loading, qid}">

                <query-items-grid
                  :item="{
                    is: 'query-item-2',
                    class: 'col-24 col-md-12 col-lg-6',
                    elements: {
                      meta: {
                        enable: true
                      },
                      title: {

                      },
                    }
                  }"
                  :items="items.slice(0, isDesktop ? 4 : 2)"
                  :queryId="qid"
                  rowClass="q-col-gutter-md"
                />

              </template>

            </query-view>

          </div>

          <div class="col-24 col-md-9 col-lg-7 q-mx-md-none">

            <div class="" style="position: relative;">

              <el-widget moreUrl="/news" title="Новости">

                <query-view
                  :query="queries.news"
                  :queryHandler="()=>$apollo.queries.news"
                  :queryResult.sync="queries.news.result"
                  :queryState.sync="queries.news.state"
                  queryId="page.front.news"
                  ref="news"
                >
                  <template v-slot:default="{items, loading, firstLoading, qid}">

                    <el-skeleton v-if="firstLoading"/>

                    <component
                      v-bind="bindNewsInner"
                      visible
                    >
                      <div class="q-pr-md">
                        <query-items-grid
                          :item="{
                            is: 'query-item-4',
                            class: 'col-24',
                            elements: {
                              content: {
                                class: 'q-py-md s-border-bottom'
                              }
                            }
                          }"
                          :items="items"
                          :queryId="qid"
                        />
                      </div>

                    </component>

                  </template>

                </query-view>

                <div class="row q-mt-lg q-col-gutter-x-md">

                  <div class="col col-shrink">
                    <q-btn
                      :loading="queries.news.state.isLoading"
                      @click="$refs.news.onNavMore()"
                      class="full-width q-px-lg"
                      color="primary"
                      dense
                      :icon="$icons.fasAngleDown"
                      label="еще"
                      outline
                    ></q-btn>
                  </div>

                  <div class="col col-grow">
                    <q-btn
                      class="full-width"
                      color="primary"
                      dense
                      label="все новости"
                      outline
                      to="/feed/all"
                    ></q-btn>
                  </div>

                </div>

              </el-widget>

              <ad-zone
                :ads="adsByZoneSlug['2']"
                :limit="2"
                class="q-pt-md"
                zoneCode="2"
              >
                <template v-slot:default="{items}">
                  <query-items-grid
                    :item="{
                     class: 'col-24 q-mb-sm',
                    }"
                    :items="items"
                    class="q-gutter-lg"
                  />
                </template>
              </ad-zone>



            </div>

          </div>

        </div>

      </div>

      <div class="container q-mb-xl">

        <CQueryViewFrontPresshall
          :query="queries.catPresshall"
          :widget="{
            title: 'Пресс-центр',
            moreUrl: '/hub/press-zall/',
            moreLabel: 'все',
          }"
        />

      </div>

      <div
        v-if="false"
        :style="{backgroundImage:'url(/statics/bt1.jpg)'}"
        class=" q-mb-xl q-mt-xl turizm"
        id="b-cat-turizm"
        style="background-size: cover;"
      >

        <div class="container">

          <div class="row q-mb-lg " style="">

            <div class="col-24 col-md-12">

              <div class="turizm__main q-px-sm q-px-sm-md q-px-md-xl q-py-lg q-py-lg-xl">

                <a
                  class="turizm__main__title s-font-4xl s-font-xxs-5xl s-font-sm-6xl s-font-md-7xl s-font-lg-8xl q-pt-lg-lg"
                  href="/turizm/">Все грани туризма</a>

                <a class="turizm__main__more s-font-xl s-font-sm-xxl q-mt-lg q-py-sm q-px-md" href="/turizm/">перейти
                  на
                  страницу проекта</a>

              </div>

            </div>

            <div class="col-6 " v-if="isDesktop">

              <div class="turizm__block q-pt-lg q-pb-lg q-pr-sm">

                <div class="turizm__block__header q-mb-sm">Как отдохнуть</div>

                <ul class="turizm__block__items">
                  <li><a href="/turizm/adventureandtravel">Приключения и путешествия</a></li>
                  <li><a href="/turizm/event-tourism">Событийный туризм</a></li>
                  <li><a href="/turizm/religious">Религиозный туризм</a></li>
                  <li><a href="/turizm/gastronomic-tourism">Гастрономический туризм</a></li>
                  <li><a href="/turizm/business-tourism">Деловой туризм</a></li>
                  <li><a href="/turizm/rural-tourism">Сельский туризм</a></li>
                  <li><a href="/turizm/kultural-tourism">Культурно-познавательный туризм</a></li>
                  <li><a href="/turizm/sports-tourism">Спортивный туризм</a></li>
                  <li><a href="/turizm/wellness-turizm">Оздоровительный туризм</a></li>
                  <li><a href="/turizm/ecological-tourism">Экологический туризм</a></li>
                </ul>

              </div>

            </div>

            <div class="col-6 " v-if="isDesktop">

              <div class="turizm__block q-pt-lg q-pb-lg q-pr-xl">

                <div class="turizm__block__header q-mb-sm">Байкальское гостеприимство</div>

                <ul class="turizm__block__items">
                  <li><a href="/turizm/hotels">Гостиницы</a></li>
                  <li><a href="/turizm/fud">Рестораны</a></li>
                  <li><a href="/turizm/travelagencies">Турфирмы</a></li>
                </ul>

              </div>

            </div>

          </div>

        </div>

      </div>

      <q-no-ssr>

          <div class="q-mb-xl q-py-lg bg-grey-10 text-white s-dark" id="b-video">
            <div class="container">

              <CQueryViewVideoPlayer
                :query="queries.video"
                :queryHandler="()=>$apollo.queries.video"
                :queryResult.sync="queries.video.result"
                :queryState.sync="queries.video.state"
                :widget="{title: 'Видео', moreUrl: '/video', moreLabel: 'все видео'}"
              />

            </div>
          </div>

      </q-no-ssr>

      <LazyHydrate when-visible>

        <div>

          <div class="container q-mb-sm q-mb-md-xl" id="b-cat-social">

            <div class="row q-col-gutter-lg q-mb-lg">

              <div class="col-24 col-sm-17">

                <CQueryViewCat1
                  :query="queries.catSocial"
                  :widget="{title: 'Общество', moreUrl: '/category/social', moreLabel: 'к рубрике'}"
                  queryId="page.front.catSocial"
                  :secondaryLimit="10"
                />

                <div class="q-mt-md" style="text-align:center;">
                  <a href="https://vk.com/app7785085?mt_adset=all&mt_campaign=dd&mt_click_id=mt-zdras5-1640163095-2970297024&mt_creative=banner&mt_network=federal&mt_sub1=social&utm_campaign=dd&utm_content=banner&utm_medium=social&utm_source=federal&utm_term=all#mt_campaign=dd&mt_adset=all&mt_network=federal&mt_creative=banner&mt_sub1=social" target="_blank" class=""><img src="/statics/banners/dd-728x90.gif"/></a>
                </div>

              </div>

              <div class="col-24 col-sm-7">

                <el-widget class="q-mb-xl" moreLabel="все" moreUrl="/quotes" title="Цитаты">
                  <query-items-swiper
                    :item="{
                      is: 'query-item-quote',
                    }"
                    :items="queries.quotes.result.nodes"
                    :slider="{
                      adaptiveHeight: true
                    }"
                    paginationStyle="bottom:0px;"
                    v-if="queries.quotes.result"
                  />
                </el-widget>

                <a href="https://www.ogirk.ru/2023/04/03/kontrakt-na-uspeshnuju-zhizn/" target="_blank" style="display:block;" class="q-mt-lg q-mb-lg">
                  <img src="/wp-content/uploads/sm-ad-item/2022/06/photo_2023-04-05_10-06-13.jpg"/>
                </a>

                <ad-zone-slider
                  :ads="adsByZoneSlug['4']"
                  :limit="3"
                  class="q-mb-lg q-pt-md"
                  zoneCode="4"
                />

              </div>

            </div>

          </div>

          <div class="container q-mb-xl" id="b-landings">

            <CQueryViewFrontProjects
              :query="queries.hubsLandings"
              :widget="{title: 'Лендинги'}"
            />

          </div>

          <div class="container q-mb-xl" id="b-projects">

            <CQueryViewFrontProjects
              :query="queries.hubsProjects"
              :widget="{title: 'Проекты'}"
            />

          </div>

          <div class="container q-mb-xl" id="b-cat-politics">

            <div class="row q-col-gutter-lg q-mb-lg">

              <div class="col-24 col-md-17">

                <CQueryViewCat1
                  :query="queries.catVlast"
                  :widget="{title: 'Власть', moreUrl: '/category/politics', moreLabel: 'к рубрике'}"
                />

              </div>

              <div class="col-24 col-md-7">

                <el-widget class="" title="Информация">

                  <ad-zone
                    :ads="adsByZoneSlug['11']"
                    :limit="3"
                    class=""
                    zoneCode="11"
                  >
                    <template v-slot:default="{items}">
                      <query-items-grid
                        :item="{
                         class: 'col-24'
                        }"
                        :items="items"
                        rowClass="q-col-gutter-md"
                      />
                    </template>
                  </ad-zone>

                  <ad-zone
                    :ads="adsByZoneSlug['5']"
                    :limit="3"
                    class="q-pt-lg"
                    zoneCode="5"
                  >
                    <template v-slot:default="{items}">
                      <query-items-grid
                        :item="{
                         class: 'col-24'
                        }"
                        :items="items"
                        rowClass="q-gutter-md"
                      />
                    </template>
                  </ad-zone>

                  <q-btn
                      class="q-py-sm q-mt-lg bg-warning full-width"
                      style="font-size: 23px;"
                      unelevated
                      text-color="white"
                      @click="$store.dispatch('dialogShow', ['app/contacts'])"
                  >
                    Связаться с рекламным отделом
                  </q-btn>

                </el-widget>



              </div>

            </div>

          </div>

          <div class="q-mb-xl" id="b-topics">

            <CQueryViewFrontTopics
              :query="queries.hubsTopics"
              :widget="{
        title: 'Сюжеты',
        moreUrl: '/topics',
        moreLabel: 'все',
      }"
            />

          </div>

          <div class="container q-mb-xl" id="b-cat-incident">

            <div class="row q-col-gutter-lg">

              <div class="col-24 col-md-17">

                <CQueryViewCat2
                  :query="queries.catIncedents"
                  :widget="{title: 'Происшествия', moreUrl: '/category/incident', moreLabel: 'к рубрике'}"
                />

              </div>

              <div class="col-24 col-md-7">


                <el-widget class="q-mb-xl" title="Читаемое" v-if="false">

                  <query-view
                    :queryResult="queries.popular.result"
                    :queryState="queries.popular.state"
                  >
                    <template v-slot:default="{items, loading}">

                      <query-items-grid
                        :item="{
                          class: 'col-24',
                          is: 'query-item-index',
                          elements: {

                          }
                        }"
                        :items="items"
                        rowClass="q-col-gutter-md"
                      ></query-items-grid>

                    </template>

                  </query-view>

                </el-widget>

                <el-widget title="Важное">

                  <query-view
                    :queryResult="queries.important.result"
                    :queryState="queries.important.state"
                  >
                    <template v-slot:default="{items, loading}">

                      <query-items-grid
                        :item="{
                  class: 'col-24',
                  is: 'query-item-3',
                  elements: {
                    row: {
                      class: 'q-col-gutter-md'
                    },
                    content: {
                      class: 'q-py-md s-border-bottom'
                    }
                  }
                }"
                        :items="items"
                      ></query-items-grid>

                    </template>

                  </query-view>

                </el-widget>

                <a href="http://irkobl.tilda.ws/85letirkutskoyoblasti" style="display: block;margin: 25px 0 25px 0;">
                  <img src="http://www.ogirk.ru/wp-content/uploads/2022/12/photo_2022-12-12_13-38-46.jpg"/>
                </a>


              </div>

            </div>

          </div>

        </div>

      </LazyHydrate>

      <div
        class="q-mb-xl q-py-lg bg-grey-10 text-white s-dark"
        id="b-fotoreports"
        v-intersection="{ handler: onIntersectFotoreports, cfg: {rootMargin: '500px 0px 0px 0px'} }"
      >
        <div class="container">
          <CQueryViewFrontFotoreports
            :query="queries.fotoreports"
            :widget="{title: 'Фоторепортажи', moreUrl:'/foto', moreLabel: isDesktop ? 'другие фоторепортажи' : 'все'}"
          />
        </div>
      </div>

      <div class="container q-mb-xl" id="b-cat-territory"
           v-intersection="{ handler: onIntersectTerritory, cfg: {rootMargin: '1000px 0px 0px 0px'} }">

        <CQueryViewRegionsMap
          :query="queries.regions"
          :widget="{title: 'Территории', moreUrl: '/category/territory', moreLabel: 'в раздел'}"
          height="600px"
        />

      </div>

      <LazyHydrate when-visible>

        <div>


          <div class="container" id="b-cat-economic">

            <div class="row q-col-gutter-lg">

              <div class="col-24 col-md-17">

                <CQueryViewCat2
                  :query="queries.catEconomic"
                  :widget="{title: 'Экономика', moreUrl: '/category/economic', moreLabel: 'к рубрике'}"
                />

              </div>

              <div class="col-24 col-md-7">

                <el-widget title="Новости компаний">

                  <query-view
                    :queryResult="queries.newsCompany.result"
                    :queryState="queries.newsCompany.state"
                  >
                    <template v-slot:default="{items, loading}">

                      <query-items-grid
                        :item="{
                    class: 'col-24',
                    is: 'query-item-3',
                    elements: {
                      row: {
                        class: 'q-col-gutter-md'
                      },
                      content: {
                        class: 'q-py-md s-border-bottom'
                      }
                    }
                  }"
                        :items="items"
                      ></query-items-grid>

                    </template>

                  </query-view>

                </el-widget>


              </div>

            </div>

          </div>

        </div>

      </LazyHydrate>

    </div>

  </q-page>
</template>

<script>
  import generateQueryInfo from '@common/graphql-apollo/lib/generate-query-info'
  import generatePageQueryInfo from '~module/app/lib/apollo-graphql/generate-page-query-info'

  import CQueryViewFrontPresshall from "~module/app/component/query/view/front/view-presshall";
  import CQueryViewFrontProjects from "~module/app/component/query/view/front/view-projects";
  import CQueryViewFrontTopics from "~module/app/component/query/view/front/view-topics";
  import CQueryViewFrontFotoreports from "~module/app/component/query/view/front/view-fotoreports";
  import CQueryViewVideoPlayer from "~module/app/component/query/view/view-video-player";

  import CQueryViewCat1 from "~module/app/component/query/view/view-cat-1";
  import CQueryViewCat2 from "~module/app/component/query/view/view-cat-2";

  import CQueryViewRegionsMap from "~module/app/component/query/view/view-regions-map";

  import MAdvertable from '~module/ad/mixin/advertable'
  import CPage from "~module/app/component/route";

  const CACHE_DEF = 'TEMP_MD';

  let v = 1;

  export default {
    serverCacheKey1: props => 'page.front',
    name: 'page.front',
    extends: CPage,
    mixins: [MAdvertable],
    components: {
      CQueryViewFrontProjects,
      CQueryViewFrontTopics,
      CQueryViewFrontFotoreports,
      CQueryViewVideoPlayer,
      CQueryViewCat1,
      CQueryViewCat2,
      CQueryViewFrontPresshall,
      CQueryViewRegionsMap
    },
    apollo: {

      news: generateQueryInfo('news', require('~module/entity/graphql/getPosts.gql'), {pollInterval: 1000 * 60 * 3}),
      topGrid: generatePageQueryInfo('topGrid', require('~module/entity/graphql/getPosts.gql'), {pollInterval: 1000 * 60 * 3}),
      topLine: generatePageQueryInfo('topLine', require('~module/entity/graphql/getPosts.gql'), {}),
      catSocial: generatePageQueryInfo('catSocial', require('~module/entity/graphql/getPosts.gql'), {}),
      catEconomic: generatePageQueryInfo('catEconomic', require('~module/entity/graphql/getPosts.gql'), {}),
      catVlast: generatePageQueryInfo('catVlast', require('~module/entity/graphql/getPosts.gql'), {}),
      catIncedents: generatePageQueryInfo('catIncedents', require('~module/entity/graphql/getPosts.gql'), {}),
      catPresshall: generatePageQueryInfo('catPresshall', require('~module/entity/graphql/getPosts.gql'), {}),

      newsCompany: generatePageQueryInfo('newsCompany', require('~module/entity/graphql/getPosts.gql'), {}),
      fotoreports: generateQueryInfo('fotoreports', require('~module/entity/graphql/getPosts.gql'), {}),
      video: generateQueryInfo('video', require('~module/entity/graphql/getPosts.gql'), {}),
      quotes: generatePageQueryInfo('quotes', require('~module/entity/graphql/getPosts.gql'), {}),

      hubsProjects: generatePageQueryInfo('hubsProjects', require('~module/entity/graphql/getPosts.gql'), {}),
      hubsLandings: generatePageQueryInfo('hubsLandings', require('~module/entity/graphql/getPosts.gql'), {}),
      hubsTopics: generatePageQueryInfo('hubsTopics', require('~module/entity/graphql/getPosts.gql'), {}),

      popular: generatePageQueryInfo('popular', require('~module/entity/graphql/getPosts.gql'), {}),
      important: generatePageQueryInfo('important', require('~module/entity/graphql/getPosts.gql'), {}),

      regions: generateQueryInfo('regions', require('~module/entity/graphql/getTerms.gql'), {}),
    },
    data() {
      return {
        page: {
          title: 'Официальные новости Иркутской области: общество, политика, экономика, территории'
        },
        pageQueriesLoading: 0,
        pageLoaded: false,

        queries: {

          popular: {
            vars: {
              filter: {
                type: this.$store.getters['app/defaultPostTypes'],
              },
              nav: {
                limit: 4
              },
              imageSize: 'm2',
              cache: CACHE_DEF
            },
            result: null
          },


          quotes: {
            vars: {
              filter: {
                type: this.$store.getters['app/defaultPostTypes'],
                tax: [
                  {taxonomy: "sm-role", slug: "quote"}
                ],
              },
              nav: {
                limit: 4
              },
              imageSize: 't1.5',
              cache: CACHE_DEF
            },
            state: {
              isLoading: false
            },
            result: null
          },

          hubsProjects: {
            vars: {
              filter: {
                type: 'sm-hub-post',
                tax: [{taxonomy: "sm-hub-type", slug: "projects"}],
                notArchive: true
              },
              nav: {
                limit: 20,
                sortField: "menuOrder",
                sortAscending: true
              },
              imageSize: 't1.5',
              cache: CACHE_DEF
            },
            state: {
              isLoading: false
            },
            result: null
          },

          hubsLandings: {
            vars: {
              filter: {
                type: 'sm-hub-post',
                tax: [{taxonomy: "sm-hub-type", slug: "landing"}],
                notArchive: true
              },
              nav: {
                limit: 20,
                sortField: "menuOrder",
                sortAscending: true
              },
              imageSize: 't1.5',
              cache: CACHE_DEF
            },
            state: {
              isLoading: false
            },
            result: null
          },

          hubsTopics: {
            vars: {
              filter: {
                type: 'sm-hub-post',
                tax: [{taxonomy: "sm-hub-type", slug: "topic"}],
                notArchive: true
              },
              nav: {
                limit: 3
              },
              imageSize: 't1.5',
              cache: CACHE_DEF
            },
            state: {
              isLoading: false
            },
            result: null
          },

          news: {
            vars: {
              filter: {
                type: 'post',
                tax: [{taxonomy: "sm-role", slug: "news"}],
              },
              nav: {
                limit: 20
              },
              imageSize: 't1.5',
              cache: CACHE_DEF
            },
            state: {
              isLoading: false,
              mode: null
            },
            result: null
          },

          topGrid: {
            vars: {
              filter: {
                type: this.$store.getters['app/defaultPostTypes'],
                tax: [{taxonomy: "sm-role", slug: "frontpage-top"}],
              },
              nav: {
                limit: 6
              },
              imageSize: 't1.4',
              excluder: true,
              alterable: true,
              cache: CACHE_DEF
            },
            state: {
              isLoading: false
            },
            result: null
          },

          topLine: {
            vars: {
              filter: {
                type: this.$store.getters['app/defaultPostTypes'],
                tax: [{taxonomy: "sm-role", slug: "is-daytop"}],
              },
              nav: {
                limit: 6
              },
              imageSize: 't1.5',
              await: 'topGrid',
              excluder: true,
              alterable: true,
              cache: CACHE_DEF
            },
            state: {
              isLoading: false
            },
            result: null
          },

          catSocial: {
            vars: {
              filter: {
                type: this.$store.getters['app/defaultPostTypes'],
                tax: [
                  {taxonomy: "category", slug: "social"},
                ],
              },
              nav: {
                limit: 12
              },
              imageSize: 't1.5',
              await: 'topLine',
              excluder: true,
              alterable: true,
              cache: CACHE_DEF
            },
            state: {
              isLoading: false
            },
            result: null
          },

          catVlast: {
            vars: {
              filter: {
                type: this.$store.getters['app/defaultPostTypes'],
                tax: [
                  {taxonomy: "category", slug: "politics"}
                ],
              },
              nav: {
                limit: 8
              },
              imageSize: 't1.5',
              await: 'catSocial',
              excluder: true,
              cache: CACHE_DEF
            },
            state: {
              isLoading: false
            },
            result: null
          },
          catEconomic: {
            vars: {
              filter: {
                type: this.$store.getters['app/defaultPostTypes'],
                tax: [
                  {taxonomy: "category", slug: "economic"}
                ],
              },
              nav: {
                limit: 6
              },
              imageSizes: ['t1.5', 't1.6'],
              await: 'catVlast',
              excluder: true,
              cache: CACHE_DEF
            },
            state: {
              isLoading: false
            },
            result: null
          },
          catPresshall: {
            vars: {
              filter: {
                type: this.$store.getters['app/defaultPostTypes'],
                terms: {
                  'sm-hub-term': [6207]
                },
              },
              nav: {
                limit: 6
              },
              imageSize: 't1.5',
              cache: CACHE_DEF
            },
            state: {
              isLoading: false
            },
            result: null
          },
          catIncedents: {
            vars: {
              filter: {
                type: this.$store.getters['app/defaultPostTypes'],
                tax: [
                  {taxonomy: "category", slug: "incident"}
                ],
              },
              nav: {
                limit: 6
              },
              imageSizes: ['t1.5', 't1.6'],
              cache: CACHE_DEF
            },
            state: {
              isLoading: false
            },
            result: null
          },
          newsCompany: {
            vars: {
              filter: {
                type: this.$store.getters['app/defaultPostTypes'],
                tax: [
                  {taxonomy: "sm-role", slug: "company"}
                ],
              },
              nav: {
                limit: 5
              },
              imageSize: 't1.5',
              cache: CACHE_DEF
            },
            state: {
              isLoading: false
            },
            result: null
          },

          important: {
            vars: {
              filter: {
                type: this.$store.getters['app/defaultPostTypes'],
                tax: [{taxonomy: "sm-role", slug: "frontpage-top"}],
              },
              nav: {
                limit: 2
              },
              imageSize: 't1.5',
              cache: CACHE_DEF
            },
            state: {
              isLoading: false
            },
            result: null
          },

          fotoreports: {
            vars: {
              filter: {
                type: this.$store.getters['app/defaultPostTypes'],
                format: 'gallery',
                //  haveImage: true,
              },
              nav: {
                limit: 6
              },
              imageSize: 't1.5',
              gallery: true,
              content: true,
              //view: 'public_full',
              cache: CACHE_DEF
            },
            state: {
              isLoading: false,
              skip: true
            },
            result: null
          },

          video: {
            vars: {
              filter: {
                type: this.$store.getters['app/defaultPostTypes'],
                format: 'video',
              },
              nav: {
                limit: 10
              },
              imageSize: 'm1.78',
              content: true,
              view: 'public_full',
              cache: CACHE_DEF
            },
            state: {
              isLoading: false,
              mode: null
            },
            result: null,
          },

          regions: {
            vars: {
              filter: {
                taxonomy: "category",
                parent: 1443
              },
              nav: {
                limit: 300,
                sortField: 'name',
                sortAscending: true
              },
              field: true,
              view: 'public_full',
              posts: true,
              postNav: {limit: 6},
              cache: CACHE_DEF
            },
            state: {
              isLoading: false,
              skip: true
            },
            result: null
          },
        },
        socnetsLoaded: false,
        isMounted: false
      }
    },

    methods: {
      ssrTest() {
        return process.env.SERVER ? 'server' : 'client'
      },
      onIntersectionSocnets(info) {

        if (info.isIntersecting && !this.socnetsLoaded) {
          this.socnetsLoaded = true
          VK.Widgets.Group("vk_groups", {mode: 3, no_cover: 1, width: "auto"}, 24655982);
          FB.XFBML.parse();
        }

      },
      onIntersectFotoreports(info) {
        if (info.isIntersecting) {
          this.queries.fotoreports.state.skip = false
        }
      },
      onIntersectTerritory(info) {
        if (info.isIntersecting) {

          this.queries.regions.state.skip = false
        }
      }
    },
    created() {


    },
    mounted() {
      this.isMounted = true


    },
    computed: {


      ads() {
        return this.queryAds.result && this.queryAds.result.nodes
      },

      bindNewsInner() {
        const res = {
          style: {}
        }

        if (this.isDesktop) {

          //let height = {xs: 600, sm: 700, md: 1520, lg: 750, xl: 870}[this.$q.screen.name];

          let height = {xs: 600, sm: 700, md: 1220, lg: 500, xl: 500}[this.$q.screen.name];

          //if (this.adsByZoneSlug[3]) height = height + (this.adsByZoneSlug[3].length * 140)

          res.style.height = height + 'px';
          res.is = 'q-scroll-area'

        } else {
          res.style.height = '340px';
          res.is = 'q-scroll-area'

        }


        return res
      },

    },
    watch: {
      pageQueriesLoading(v) {
        if (!v) {
          this.pageLoaded = true
        }
      },
      pageLoaded(val) {

      }
    }
  }
</script>

<style lang="scss" scoped>

  .turizm {
    color: #fff;

    a {
      color: #fff;
    }
  }

  .turizm__main__title {
    font-weight: 800;
    line-height: 1.3em;
    text-decoration: none;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
    display: block;
  }

  .turizm__main__more {
    font-size: 25px;
    border: 1px solid #FFF;
    display: inline-block;
    text-decoration: none;
    line-height: 1;
    border-radius: 8px;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
  }

  .turizm__block__header {
    font-size: 25px;
    line-height: 1.2em;
    font-weight: 800;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
  }

  .turizm__block__items {
    font-size: 19px;
    padding: 0;
    margin: 0;
    list-style-type: none;

    li {
      &:not(:last-child) {
        margin: 0 0 0.1em 0;
      }

      a {
        text-decoration: none;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
        font-weight: 600;
      }
    }
  }


</style>


