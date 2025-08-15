<template>

  <CItem :item="pageData.entity" v-if="pageData.entity"/>

</template>

<script>
  import setPageRouteData from "@common/core/lib/route/set-page-data";
  import CItem from '~module/app/component/query/item/page/post-quiz/item'
  import CPage from "~module/app/component/route";

  const loadPageData = async (pageData, ctx) => {

    try {

      let {data} = await ctx.apolloClient.query({
        query: require('~module/entity/graphql/getPost.gql'),
        fetchPolicy: 'no-cache',
        variables: {
          filter: {
            type: 'sm-quiz',
            slug: ctx.route.params.slug,
          },
          content: true,
          view: 'public_full'
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
