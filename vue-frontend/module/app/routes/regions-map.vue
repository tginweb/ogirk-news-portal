<template>
  <q-page class="q-mt-xl q-mb-xl">

    <CLayout v-if="pageData.result">


      <template v-slot:default>

        <div class="row q-col-gutter-xl">

          <div class="col-17">

            <el-widget title="Карта области">

              <CMap :entities="entities"/>

            </el-widget>

          </div>


          <div class="col-7">



          </div>


        </div>


      </template>


    </CLayout>

  </q-page>
</template>

<script>

  import CLayout from '~module/app/layout/main/page/1cols'
  import CSidebar from '~module/app/layout/main/sidebar/common'
  import CMap from '~com/map/regions'
  import setPageRouteData from "@common/core/lib/route/set-page-data";
  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";
  import CPage from "~module/app/component/route";

  const loadPageData = async (pageData, ctx) => {

    try {

      let {data} = await ctx.apolloClient.query({
        query: require('~module/entity/graphql/getTerms.gql'),
        fetchPolicy: 'no-cache',
        variables: {
          filter: {
            taxonomy: "category",
            parent: 1443
          },
          nav: {
            limit: 300,
            sortField: 'lastActivity'
          },
          posts: true,
          postNav: {limit: 3}
        },
      })

      pageData.result = data.res

    } catch (e) {
      console.log(e)
    }

    return pageData;
  }

  export default {
    extends: CPage,
    components: {
      CLayout,
      CSidebar,
      CMap,
    },
    apollo: {
      news: generateQueryInfo('news', require('~module/entity/graphql/getPosts.gql'), {}),
    },
    data() {
      return {
        queries: {
          list: {
            result: null
          },
          news: {
            vars: {
              filter: {
                type: 'post',
                tax: [{taxonomy: "sm-role", slug: "news"}],
              },
              nav: {
                limit: 14
              },
              imageSize: 't1.5'
            },
            state: {
              isLoading: false,
              mode: null
            },
            result: null
          },
        },
      }
    },
    computed: {
      entities() {
        return this.pageData.result.nodes.map(entity => {
          return {
            ...entity,
            coordinates: typeof entity.meta.coordinates === 'string' ? JSON.parse(entity.meta.coordinates) : null
          }
        })
      }
    },
    async preFetch(ctx) {
      return setPageRouteData('static', ctx, loadPageData)
    }
  }
</script>

<style lang="scss" scoped>

  .c-item {
    display: inline-block;
    border-bottom1: 0;
  }

</style>
