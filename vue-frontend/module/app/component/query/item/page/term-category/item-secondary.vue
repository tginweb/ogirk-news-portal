<template>
  <q-page class="q-mt-lg">

    <CLayout>

      <template v-slot:default>

        <el-page-header :title="item.name" :showSubmenu="screen.gt.sm" >

          <template v-slot:side>

            <CCalendarDropdown
              :date="$route.params.date"
              :filter="calendarFilter"
              :url="'/category/' + $route.params.slug + '/$date'"
            />

          </template>

        </el-page-header>

        <query-view
          :pager="{desktopType: 'prevnext', mobileType: 'loadmore'}"
          :queryHandler="()=>$apollo.queries.list"
          :queryResult.sync="queries.list.result"
          :queryState.sync="queries.list.state"
        >
          <template v-slot:default="{items, loading, state}">

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
        <CSidebar class="q-pt-sm" />
      </template>
    </CLayout>

  </q-page>
</template>

<script>
  import CItem from './../../_item-term'
  import MRoutable from '~module/app/mixin/routable'
  import CCalendarDropdown from '~module/app/component/query/filter/calendar/dropdown'
  import CLayout from '~module/app/layout/main/page/2cols'
  import CSidebar from '~module/app/layout/main/sidebar/common'
  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";


  export default {
    extends: CItem,
    mixins: [MRoutable],
    components: {
      CLayout,
      CSidebar,
      CCalendarDropdown
    },
    props: {
      data: {}
    },
    apollo: {
      list: generateQueryInfo('list', require('~module/entity/graphql/getPosts.gql'), {}, {varPath: 'cqueries.list.vars'}),
    },
    data() {
      return {
        routeQuery: 'queries.list',
        queries: {
          list: {
            result: null,
            state: {isLoading: false},
          },
        }
      }
    },
    computed: {
      cqueries() {
        return {
          list: {
            vars: {
              filter: {
                type: 'post',
                tax: [
                  {taxonomy: "category", id: this.item.nid}
                ],
                ...this.routeFilter,
              },
              nav: {
                limit: 20,
                ...this.routeNav,
              },
              imageSize: 't1.5'
            },
          },
        }
      },

      calendarFilter() {
        return {
          type: 'post',
          tax: [{taxonomy: "category", id: this.item.nid}]
        }
      }
    }
  }
</script>
