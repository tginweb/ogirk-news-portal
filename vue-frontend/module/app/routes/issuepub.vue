<template>
  <q-page class="q-mt-lg q-mb-xl">

    <CItem :item="pageData.entity" v-if="pageData.entity"/>

  </q-page>
</template>

<script>
  import setPageRouteData from "@common/core/lib/route/set-page-data";
  import CItem from '~module/app/component/query/item/page/item-post-issuepub'
  import CPage from "~module/app/component/route";

  const loadPageData = async (pageData, ctx) => {

    try {

      let {data} = await ctx.apolloClient.query({
        query: require('~module/entity/graphql/getPost.gql'),
        fetchPolicy: 'no-cache',
        variables: {
          filter: {
            slug: ctx.route.params.slug,
            taxonomy: "sm-issue-print"
          },
          termsTaxonomy: ['sm-issue'],
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
  }
</script>
