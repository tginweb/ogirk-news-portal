<template>

  <CItem :item="pageData.entity" v-if="pageData.entity"/>

</template>

<script>
  import setPageRouteData from "@common/core/lib/route/set-page-data";
  import CItem from '~module/app/component/query/item/page/post-conf/item'
  import CPage from "~module/app/component/route";

  const loadPageData = async (pageData, ctx) => {

    try {

      let {data} = await ctx.apolloClient.query({
        query: require('~module/entity/graphql/getPost.gql'),
        fetchPolicy: 'no-cache',
        variables: {
          filter: {
            type: 'sm-conference',
            slug: ctx.route.params.slug,
          },
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

