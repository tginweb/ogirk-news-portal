<template>
  <q-page class="q-mt-lg">

    <CLayout>

      <template v-if="!$route.query.next && !$route.query.previous" v-slot:top>

        <div class="q-mb-xl">

          <CQueryViewRegionsMap
            :query="queries.regions"
            :widget="{title: 'Территории', moreUrl: '/category/territory'}"
            height="600px"
          />

        </div>

        <query-items-regional
          :items="queries.top.result.nodes"
          :regions="[
            {
              outerClass: 'col-24',
              innerClass: 'row q-col-gutter-lg',
              limit: 3,
              item: {
                is: 'query-item-1',
                class: 'col-24 col-sm-8',
                expandable: true,
                elements: {
                  title: {
                    defclass: 's-font-xl'
                  }
                }
              }
            },
            {
              outerClass: 'col-24',
              innerClass: 'row q-col-gutter-lg',
              limit: 3,
              item: {
                is: 'query-item-1',
                class: 'col-24 col-sm-8',
                expandable: true,
                elements: {
                  title: {
                    defclass: 's-font-xl'
                  }
                }
              }
            },
          ]"
          class="row q-col-gutter-lg"
          v-if="!queries.top.state.isLoading && queries.top.result"
        />
        <el-skeleton v-else/>

      </template>

      <template v-slot:default>

        <el-widget
          :title="item.name"
        >
          <query-view
            :pager="{desktopType: 'prevnext', mobileType: 'loadmore'}"
            :queryHandler="()=>$apollo.queries.archive"
            :queryVars="cqueries.archive.vars"
            :queryResult.sync="queries.archive.result"
            :queryState.sync="queries.archive.state"
          >
            <template v-slot:default="{items, state, loading}">


              <query-items-grid
                :item="{
                  is: 'query-item-5',
                  class: 'col-24',
                  elements: {
                    content: {
                     class: 'q-py-lg s-border-bottom'
                    }
                  }
                }"
                :items="items"
                :queryState="state"
                v-if="!loading"
              />
              <el-skeleton v-else/>

            </template>

          </query-view>

        </el-widget>

      </template>
      <template v-slot:right>
        <CSidebar/>
      </template>

    </CLayout>

  </q-page>
</template>

<script>
  import CItem from './../../_item-term'
  import CLayout from '~module/app/layout/main/page/2cols'
  import CSidebar from '~module/app/layout/main/sidebar/common'
  import CQueryViewRegionsMap from "~module/app/component/query/view/view-regions-map";

  import MRoutable from '~module/app/mixin/routable'
  import generateQueryInfo from '@common/graphql-apollo/lib/generate-query-info'
  import generatePageQueryInfo from '~module/app/lib/apollo-graphql/generate-page-query-info'

  export default {
    extends: CItem,
    mixins: [MRoutable],
    components: {
      CLayout,
      CSidebar,
      CQueryViewRegionsMap
    },
    props: {
      data: {}
    },
    apollo: {
      top: generateQueryInfo('top', require('~module/entity/graphql/getPosts.gql'), {}, {varPath: 'cqueries.top.vars',}),
      archive: generateQueryInfo('archive', require('~module/entity/graphql/getPosts.gql'), {}, {varPath: 'cqueries.archive.vars',}),
      regions: generatePageQueryInfo('regions', require('~module/entity/graphql/getTerms.gql'), {})
    },
    data() {
      return {
        queries: {
          top: {
            result: null,
            state: {isLoading: false}
          },
          archive: {
            result: null,
            state: {isLoading: false}
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
              postNav: {limit: 6}
            },
            state: {
              isLoading: false
            },
            result: null
          },
        }
      }
    },
    watch: {
      '$apollo.loading'(val) {
        if (!val) this.$emit('loaded')
      },
    },
    computed: {

      cqueries() {
        return {
          top: {
            vars: {
              filter: {
                type: 'post',
                tax: [
                  {taxonomy: "category", id: this.item.nid, withChildren: true}
                ],
                haveImage: true,
              },
              nav: {
                limit: 6
              },
              imageSize: 't1.5'
            },
          },
          archive: {
            vars: {
              filter: {
                type: 'post',
                tax: [
                  {taxonomy: "category", id: this.item.nid, withChildren: true}
                ],
                ...this.routeFilter,
              },
              nav: {
                limit: 10,
                ...this.routeNav,
              },
              imageSize: 't1.5'
            },
          },
        }
      }
    },

  }
</script>
