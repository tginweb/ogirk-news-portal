<template>
  <q-page class="q-mt-lg q-mb-xl">

    <CLayout>

      <template v-slot:header>

        <el-page-header :title="pageTitle" :showSubmenu="screen.gt.sm">

          <template v-slot:side>

            <CCalendarDropdown
              :date="$route.params.date"
              :filter="calendarFilter"
              :url="'/issue/' + $route.params.slug + '/$date'"
            />

          </template>

        </el-page-header>

      </template>
      <template v-slot:default>


        <query-view
          :pager="{desktopType: 'prevnext', mobileType: 'loadmore', align: 'center'}"
          :queryHandler="()=>$apollo.queries.list"
          :queryResult.sync="queries.list.result"
          :queryState.sync="queries.list.state"
        >
          <template v-slot:default="{items, state, loading, onNavMore}">

            <query-items-grid
              :item="{
                class: 'col-24 col-md-12',
                is: 'query-item-district-issuepub',
                elements: {
                  row: {
                    class: 'q-col-gutter-md'
                  }
                }
              }"
              :items="items"
              rowClass="q-col-gutter-x-lg q-col-gutter-y-xl"

            />

          </template>

        </query-view>


      </template>
    </CLayout>

  </q-page>
</template>

<script>
  import CLayout from '~module/app/layout/main/page/1cols'
  import CCalendarDropdown from '~module/app/component/query/filter/calendar/dropdown'
  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";
  import setPageRouteData from "@common/core/lib/route/set-page-data";
  import MRoutable from '~module/app/mixin/routable'
  import CPage from "~module/app/component/route";

  const loadPageData = async (pageData, ctx) => {

    const issueCode = ctx.route.params.slug

    if (issueCode && issueCode !== 'all') {

      try {

        let {data} = await ctx.apolloClient.query({
          query: require('~module/entity/graphql/getTermPage.gql'),
          fetchPolicy: 'no-cache',
          variables: {
            filter: {
              slug: issueCode,
              taxonomy: "sm-other-issue"
            }
          },
        })

        pageData.entity = data.res
      } catch (e) {
        console.log(e)
      }
    }

    return pageData;
  }

  export default {
    name: 'page.other-issue',
    extends: CPage,
    mixins: [MRoutable],
    components: {
      CLayout,
      CCalendarDropdown
    },
    apollo: {
      list: generateQueryInfo('list', require('~module/entity/graphql/getPosts.gql'), {}, {varPath: 'cqueries.list.vars'}),
    },
    data() {
      return {
        page: {
          title: 'Районные издания'
        },
        queries: {
          list: {
            result: null,
            state: {isLoading: false},
          },
        },
        calendarDate: null
      }
    },
    computed: {
      cqueries() {
        return {
          list: {
            vars: {
              filter: {
                type: 'sm-other-issue-print',
                tax: this.pageData.entity ? [{taxonomy: "sm-other-issue", id: this.pageData.entity.nid}] : [],
                ...this.routeFilter,
              },
              nav: {
                limit: 20,
                ...this.routeNav,
              },
              imageHook: false,
              imageSize: 'c152x260',
              termsTaxonomy: ['sm-other-issue'],
              cache: 'TEMP_LG'
            },
            result: null
          },
        }
      },

      calendarFilter() {
        return this.pageData.entity ? {
          type: 'sm-other-issue-print',
          tax: [{taxonomy: "sm-other-issue", id: this.pageData.entity.nid}]
        } : null
      }
    },
    async preFetch(ctx) {
      return setPageRouteData('static', ctx, loadPageData)
    },
  }
</script>
