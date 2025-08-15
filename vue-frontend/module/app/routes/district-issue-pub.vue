<template>
  <q-page class="q-mt-lg q-mb-xl">

    <CItem :item="pageData.entity" v-if="pageData.entity"/>

  </q-page>
</template>

<script>
  import setPageRouteData from "@common/core/lib/route/set-page-data";
  import CItem from '~module/app/component/query/item/page/item-post-district-issuepub'
  import CPage from "~module/app/component/route";

  const loadPageData = async (pageData, ctx) => {

    try {

      console.log('ctx.route.params', ctx.route.params.nid)
      let {data} = await ctx.apolloClient.query({
        query: require('~module/entity/graphql/getPost.gql'),
        fetchPolicy: 'no-cache',
        variables: {
          filter: {
            nid: ctx.route.params.nid,
            taxonomy: "sm-other-issue-print"
          },
          termsTaxonomy: ['sm-other-issue'],
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
    components: {
      CItem,
    },
    async preFetch(ctx) {
      return setPageRouteData('static', ctx, loadPageData)
    },
    computed: {
      pageTitle() {
        return 'sss'
      }
    }
  }
</script>
