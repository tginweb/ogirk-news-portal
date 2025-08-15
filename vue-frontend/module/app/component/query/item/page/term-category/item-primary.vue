<template>
  <q-page class="q-mt-lg">


    <CLayout>

      <template v-if="!$route.query.next && !$route.query.previous" v-slot:top>

        <query-items-regional
          :items="queries.top.result.nodes"
          :regions="[
            {
              outerClass: 'col-24',
              innerClass: 'row q-col-gutter-lg',
              limit: 2,
              item: {
                is: 'query-item-1',
                class: 'col-24 col-sm-12',
                expandable: true,
                elements: {
                  title: {
                    defclass: 's-font-lg s-font-sm-xl s-font-md-3xl'
                  }
                }
              }
            },
            {
              outerClass: 'col-24',
              innerClass: 'row q-col-gutter-lg',
              limit: 6,
              item: {
                is: 'query-item-1',
                class: 'col-24 col-sm-12 col-md-12 col-lg-8',
                expandable: true,
                elements: {
                  title: {
                    defclass: 's-font-lg s-font-sm-xl s-font-md-xl'
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
            :queryResult.sync="queries.archive.result"
            :queryState.sync="queries.archive.state"
            :queryVars="cqueries.archive.vars"
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
  import MRoutable from '~module/app/mixin/routable'
  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";

  export default {
    name: 'item.page.term-category',
    extends: CItem,
    mixins: [MRoutable],
    components: {
      CLayout,
      CSidebar
    },
    props: {
      data: {}
    },
    apollo: {
      top: generateQueryInfo('top', require('~module/entity/graphql/getPosts.gql'), {}, {varPath: 'cqueries.top.vars',}),
      archive: generateQueryInfo('archive', require('~module/entity/graphql/getPosts.gql'), {}, {varPath: 'cqueries.archive.vars',}),
    },
    data() {
      return {
        queriesLoading: 0,
        queries: {
          top: {
            result: null,
            state: {isLoading: false}
          },
          archive: {
            result: null,
            state: {isLoading: false}
          },
        }
      }
    },
    watch: {
      '$apollo.loading'(val) {
        if (!val) this.$emit('loaded')
      },
      queriesLoading(val) {
        if (!val) this.$emit('loaded')
      }
    },
    computed: {

      cqueries() {
        return {
          top: {
            vars: {
              filter: {
                type: 'post',
                tax: [
                  {taxonomy: "category", id: this.item.nid, withChildren: true},
                  //   {taxonomy: "sm-role", slug: "rubric-top"}
                ],
              },
              nav: {
                limit: 8
              },
              imageSize: 't1.5',
              excluder: !this.routePaginated ? true : false,
              cache: 'TEMP_LG'
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
                limit: 20,
                ...this.routeNav,
              },
              imageSize: 't1.5',
              await: !this.routePaginated ? 'top' : null,
              excluder: !this.routePaginated ? true : false,
              cache: 'TEMP_LG'
            },
          },
        }
      }
    },

  }
</script>
