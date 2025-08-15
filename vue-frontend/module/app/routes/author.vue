<template>
  <q-page class="q-mt-lg q-mb-xl">

    <CLayout v-if="pageData.entity">
      <template v-slot:header>

        <el-page-header :title="pageTitle">
        </el-page-header>

      </template>
      <template v-slot:default>
        <query-view
          :pager="{desktopType: 'prevnext', mobileType: 'loadmore'}"
          :query="queries.posts"
          :queryHandler="()=>$apollo.queries.posts"
          :queryResult.sync="queries.posts.result"
          :queryState.sync="queries.posts.state"
        >
          <template v-slot:default="{items, loading, onNavMore}">

            <query-items-grid
              :item="{
                is: 'query-item-5',
                class: 'col-24',
                elements: {
                  content: {
                    class: 'q-py-lg s-border-bottom'
                  },
                  row: {
                    class: 'q-col-gutter-md'
                  }
                }
              }"
              :items="items"
              :queryState="queries.posts.state"
              v-if="!loading"
            />
            <el-skeleton v-else/>

          </template>

        </query-view>

      </template>
      <template v-slot:right>
        <CSidebar/>
      </template>
    </CLayout>

  </q-page>
</template>

<script>
  import CCalendarDropdown from '~module/app/component/query/filter/calendar/dropdown'
  import CLayout from '~module/app/layout/main/page/2cols'
  import CSidebar from '~module/app/layout/main/sidebar/common'
  import setPageRouteData from "@common/core/lib/route/set-page-data";
  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";
  import CPage from "~module/app/component/route";
  import MRoutable from "~module/app/mixin/routable";

  const loadPageData = async (pageData, ctx) => {

    try {

      let {data} = await ctx.apolloClient.query({
        query: require('~module/entity/graphql/getTermPage.gql'),
        fetchPolicy: 'no-cache',
        variables: {
          filter: {
            slug: ctx.route.params.slug,
            taxonomy: "sm-author"
          }
        },
      })

      pageData.entity = data.res
    } catch (e) {

      console.log(e)
    }

    return pageData;
  }

  export default {
    extends: CPage,
    mixins: [MRoutable],
    components: {
      CLayout,
      CSidebar,
      CCalendarDropdown
    },
    apollo: {
      posts: generateQueryInfo('posts', require('~module/entity/graphql/getPosts.gql'), {}, {
        varPath: 'cqueries.posts.vars',
      }),
    },
    data() {
      return {
        queries: {
          posts: {
            result: null,
            state: {isLoading: false}
          },
        }
      }
    },
    computed: {

      cqueries() {
        return {
          posts: {
            vars: {
              filter: {
                type: 'post',
                tax: [
                  {taxonomy: "sm-author", id: this.pageData.entity ? this.pageData.entity.nid : -100}
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
      },

      calendarFilter() {
        return this.pageData.entity ? {
          type: 'post',
          tax: [{taxonomy: "sm-author", id: this.pageData.entity.nid}]
        } : null
      }
    },
    async preFetch(ctx) {
      return setPageRouteData('static', ctx, loadPageData)
    },
  }
</script>

<style lang="scss" scoped>


</style>
