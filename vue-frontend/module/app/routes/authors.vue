<template>
  <q-page class="q-mt-lg q-mb-xl">

    <CLayout v-if="pageData.result">

      <template v-slot:header>
        <el-page-header :title="pageTitle"/>
      </template>

      <template v-slot:default>

        <query-items-grid
          :item="{
            class: 'col-24',
            is: 'query-item-term',
            elements: {
              content: {
                class: 'q-py-md s-border-bottom'
              }
            }
          }"
          :items="pageData.result.nodes"
          rowClass="q-col-gutter-md"
        ></query-items-grid>

      </template>

      <template v-slot:right>
        <CSidebar/>
      </template>

    </CLayout>

  </q-page>
</template>

<script>

  import CLayout from '~module/app/layout/main/page/2cols'
  import CSidebar from '~module/app/layout/main/sidebar/common'
  import setPageRouteData from "@common/core/lib/route/set-page-data";
  import CPage from "~module/app/component/route";

  const loadPageData = async (pageData, ctx) => {

    try {

      let {data} = await ctx.apolloClient.query({
        query: require('~module/entity/graphql/getTerms.gql'),
        fetchPolicy: 'no-cache',
        variables: {
          filter: {
            taxonomy: "sm-author",
            authorList: true
          },
          nav: {
            limit: 30,
            sortField: 'lastActivity'
          },
          posts: true,
          postNav: {limit: 3},
          cache: 'TEMP_LG'
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
      CSidebar
    },
    data() {
      return {
        page: {
          title: 'Авторы'
        }
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
