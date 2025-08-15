<template>
  <q-page class="q-mb-xl" :class="{
    'q-mt-lg': routePaginated
  }">

    <div class="q-mb-xl q-py-lg bg-grey-10 text-white s-dark" id="b-video" v-if="!routePaginated && screen.gt.md">
      <div class="container">
        <CQueryViewVideoPlayer
          :query="queries.player"
          :queryHandler="()=>$apollo.queries.player"
          :queryResult.sync="queries.player.result"
          :queryState.sync="queries.player.state"
          :widget="{title: 'Видео'}"
        />
      </div>
    </div>

    <div class="container" :class="{'q-mt-lg': screen.lt.md}">

      <el-widget title="Все видео">

        <query-view
          :pager="{desktopType: 'prevnext', mobileType: 'loadmore', align: 'center'}"
          :queryHandler="()=>$apollo.queries.posts"
          :queryResult.sync="queries.posts.result"
          :queryState.sync="queries.posts.state"
        >
          <template v-slot:default="{items, state, loading, onNavMore}">

            <query-items-grid
              :item="{
                is: 'query-item-1',
                elements: {
                  title: {
                    defclass: 's-font-xl'
                  }
                }
              }"
              :items="items"
              itemClass="col-24 col-md-8"
              rowClass="q-col-gutter-lg"
            />

          </template>

        </query-view>


      </el-widget>

    </div>

  </q-page>
</template>

<script>
  import CPageLayout from '~module/app/layout/main/page/1cols'
  import CPageSidebar from '~module/app/layout/main/sidebar/common'
  import CQueryViewVideoPlayer from "~module/app/component/query/view/view-video-player";

  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";
  import CPage from "~module/app/component/route";
  import MRoutable from '~module/app/mixin/routable'

  export default {
    name: 'page.video',
    extends: CPage,
    mixins: [MRoutable],
    components: {
      CPageLayout,
      CPageSidebar,
      CQueryViewVideoPlayer
    },
    apollo: {

      player: generateQueryInfo('player', require('~module/entity/graphql/getPosts.gql')),
      posts: generateQueryInfo('posts', require('~module/entity/graphql/getPosts.gql'), {}, {varPath: 'cqueries.posts.vars'}),

    },
    data() {
      return {
        page: {
          title: 'Видео'
        },
        queries: {
          player: {
            vars: {
              filter: {
                type: 'post',
                format: 'video',
              },
              nav: {
                limit: 10
              },
              imageSize: 'm1.78',
              content: true,
              view: 'public_full',
              cache: 'TEMP_LG'
            },
            state: {
              isLoading: false,
              mode: null
            },
            result: null,
          },
          posts: {
            state: {isLoading: false},
            result: null
          },
        },
        calendarDate: null
      }
    },
    computed: {
      cqueries() {
        return {
          posts: {
            vars: {
              filter: {
                type: 'post',
                format: 'video',
                ...this.routeFilter,
              },
              nav: {
                limit: 20,
                ...this.routeNav,
              },
              imageSize: 't1.4',
              cache: 'TEMP_LG'
            },
          },
        }
      },
    },

  }
</script>
