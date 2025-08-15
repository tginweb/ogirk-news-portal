<template>
  <q-page class="q-mt-lg">

    <CLayout>

      <template v-slot:header>

        <el-page-header :title="item.name"/>

      </template>

      <template v-slot:default>

          <query-view
            :queryHandler="()=>$apollo.queries.list"
            :queryResult.sync="queries.list.result"
            :queryState.sync="queries.list.state"
            :pager="{desktopType: 'prevnext', mobileType: 'loadmore'}"
          >
            <template v-slot:default="{items, state, loading, onNavMore}">

              <query-items-grid
                :items="items"
                :queryState="state"
                :item="{
                  is: 'query-item-special'
                }"
                rowClass="q-col-gutter-xl"
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
  import CItem from './../../_item-term'
  import CLayout from '~module/app/layout/main/page/1cols'
  import CSidebar from '~module/app/layout/main/sidebar/common'
  import MRoutable from '~module/app/mixin/routable'
  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";

  export default {
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
      list: generateQueryInfo('list', require('~module/entity/graphql/getPosts.gql'), {}, {varPath: 'cqueries.list.vars',}),
    },
    data() {
      return {
        queries: {
          list: {
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
    },
    computed: {

      cqueries() {
        return {
          list: {
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
