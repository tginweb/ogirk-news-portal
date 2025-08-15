<template>
  <q-page class="q-mt-lg q-mb-xl">

    <CLayout>
      <template v-slot:header>

        <el-page-header :title="pageTitle"/>

      </template>
      <template v-slot:default>

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
              v-if="!loading"
            />
            <el-skeleton v-else/>

          </template>

        </query-view>


      </template>
    </CLayout>

  </q-page>
</template>

<script>
  import CLayout from '~module/app/layout/main/page/1cols'
  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";
  import CPage from "~module/app/component/route";
  import MRoutable from '~module/app/mixin/routable'

  export default {
    name: 'page.foto',
    extends: CPage,
    mixins: [MRoutable],
    components: {
      CLayout,
    },
    apollo: {
      posts: generateQueryInfo('posts', require('~module/entity/graphql/getPosts.gql'), {}, {varPath: 'cqueries.posts.vars'}),
    },
    data() {
      return {
        page: {
          title: 'Фоторепортажи'
        },
        queries: {
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
                format: 'gallery',
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
    }
  }
</script>
