<template>
  <q-page class="q-mt-lg">

    <CLayout>

      <template v-slot:header>
        <el-page-header :title="item.name" :showSubmenu="false"/>
      </template>

      <template v-slot:default>

        <div v-if="!$route.query.next && !$route.query.previous" class="q-mb-lg">

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
                    defclass: 's-font-xl'
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
                class: 'col-24 col-sm-12',
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

        </div>

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

      </template>

      <template v-slot:right>

        <CQueryMap
          :query="queries.regions"
          :widget="{title: 'Информация', moreUrl: '/category/social', moreLabel: 'к рубрике'}"
          height="350px"
          :value="item.nid"
          class="q-mt-lg q-mb-xl"
        />

        <el-widget title="Все регионы" v-if="queries.regions.result">

          <q-scroll-area
            :style="{height: '400px'}"
            visible
          >
            <div
              :key="entity.nid"
              class="places-list__item"
              v-for="entity of queries.regions.result.nodes"
            >
              <router-link
                :to="entity.url"
                class="s-link text-accent"
              >
                {{entity.name}}
              </router-link>
            </div>
          </q-scroll-area>

        </el-widget>


      </template>

    </CLayout>

  </q-page>
</template>

<script>
  import CItem from './../../_item-term'
  import CLayout from '~module/app/layout/main/page/2cols'
  import CSidebar from '~module/app/layout/main/sidebar/common'
  import CQueryMap from '~module/app/component/query/view/view-regions-cat-map'
  import MRoutable from '~module/app/mixin/routable'
  import generateQueryInfo from '@common/graphql-apollo/lib/generate-query-info'
  import generatePageQueryInfo from '~module/app/lib/apollo-graphql/generate-page-query-info'

  export default {
    extends: CItem,
    mixins: [MRoutable],
    components: {
      CLayout,
      CSidebar,
      CQueryMap
    },
    props: {
      data: {}
    },
    apollo: {
      top: generateQueryInfo('top', require('~module/entity/graphql/getPosts.gql'), {}, {varPath: 'cqueries.top.vars',}),
      archive: generateQueryInfo('archive', require('~module/entity/graphql/getPosts.gql'), {}, {varPath: 'cqueries.archive.vars',}),
      regions: generatePageQueryInfo('regions', require('~module/entity/graphql/getTerms.gql'), {}),
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
                limit: 8
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

<style lang="scss" scoped>

  .places-list__item {
    padding: 6px 0 6px 0;
    &.active {
      background-color: $primary;
      padding: 3px 6px;

      a {
        color: #fff !important;
      }
    }
  }


</style>
