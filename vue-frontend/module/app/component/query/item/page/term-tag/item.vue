<template>
  <div>

    <div class="container">

      <el-page-header
        :title="item.name"
        class="q-mb-md"
      >
      </el-page-header>

      <div class="i-content">

        <div class="row q-col-gutter-x-xl q-col-gutter-y-md">

          <div class="col-24 col-md-15">

            <div class="i-feed">

              <query-view
                :pager="{desktopType: 'prevnext', mobileType: 'loadmore', align: 'center'}"
                :queryHandler="()=>$apollo.queries.posts"
                :queryResult.sync="queries.posts.result"
                :queryState.sync="queries.posts.state"
              >
                <template v-slot:default="{items, state, loading, onNavMore}">

                  <q-timeline
                    class="q-my-none"
                    v-if="items"
                  >

                    <q-timeline-entry
                      :key="item._id"
                      :subtitle="datetimeFormatDefault(item.created)"
                      class="i-post"
                      v-for="item of items"
                    >
                      <query-item-6
                        :item="item"
                        class=""
                      />
                    </q-timeline-entry>

                  </q-timeline>

                </template>

              </query-view>

            </div>

          </div>

          <div :class="{'order-first': screen.lt.md}" class="col-25 col-md-9" id="col-right">

            <div class=" q-pb-md" v-hc-sticky="{stickTo: '#col-right', followScroll: true, top: 0}" v-if="hasAbout">

              <div class="i-about q-mt-md-xl q-px-xl q-py-xl shadow-2">


                <div v-if="item.nid===7895">
                  В Иркутской области в ближайшее время стартует массовая вакцинация от
                  коронавируса. Как можно записаться на прививку? Как и где поставить? Есть ли
                  ограничения? Как готовиться к вакцинации? Ответы на эти и другие вопросы вы
                  найдете на этой странице.
                </div>
                <div v-else-if="item.nid===7951">

                  <p>
                    Голосование началось 15 марта и продлится до 30 апреля 2024 года.
                    Участвовать в голосовании могут все жители с 14 лет и старше,
                    проживающие на территории 18 муниципальных образований Иркутской области.
                    Формат голосования с 18 марта 2024 года – онлайн, на единой федеральной платформе
                    <a href="https://38.gorodsreda.ru" target="_blank">38.gorodsreda.ru</a>
                  </p>

                </div>

                <CDosie
                  :data="irkipedia"
                  :item="item"
                  v-if="irkipedia"
                />

              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

  </div>
</template>

<script>
  import CItem from './../../_item-term'
  import CDosie from './parts/dosie'

  import CPageLayout from '~module/app/layout/main/page/1cols'
  import CPageSidebar from '~module/app/layout/main/sidebar/common'
  import MRoutable from '~module/app/mixin/routable'
  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";
  import formatDefault from '~module/app/lib/util/date/format-default'

  export default {
    extends: CItem,
    mixins: [MRoutable],
    components: {
      CPageLayout,
      CPageSidebar,
      CDosie
    },
    props: {
      irkipedia: {}
    },
    apollo: {
      posts: generateQueryInfo('posts', require('~module/entity/graphql/getPosts.gql'), {}, {varPath: 'cqueries.posts.vars'}),
    },
    data() {
      return {
        queries: {
          posts: {
            result: null,
            state: {isLoading: false},
          },
        },
        calendarDate: null,
        datetimeFormatDefault: formatDefault,
      }
    },
    computed: {

      hasAbout() {
        return !!this.irkipedia || this.item.nid === 7895 || this.item.nid === 7951;
      },

      cqueries() {
        return {
          posts: {
            vars: {
              filter: {
                type: 'post',
                tax: [{taxonomy: "post_tag", id: this.item.nid}],
                ...this.routeFilter,
              },
              nav: {
                limit: 30,
                ...this.routeNav,
              },
              imageSize: 't2.4',
              imageHook: false,
              cache: 'TEMP_LG'
            },
            result: null
          },
        }
      }
    },
  }
</script>

<style lang="scss" scoped>

  .i-header {
    position: relative;
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center center;
    color: #fff;
    height: 400px;

    a {
      color: #fff;
    }
  }

  .i-title {
    font-size: 48px;
    font-weight: bold;
    text-align: center;
  }

  .i-subtitle {
    font-size: 34px;
    font-weight1: bold;
    text-align: center;
  }

  .i-about {
    position: relative;
    background: #FFF;
    max-width: 900px;
    border-radius: 5px;
  }

</style>
