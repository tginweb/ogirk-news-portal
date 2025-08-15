<template>
  <q-page class="q-mb-xl">


    <div v-if="pageData.entity">

      <CItem :item="pageData.entity"/>

    </div>

  </q-page>
</template>

<script>
  import CItem from '~module/app/component/query/item/page/item-post-hub'

  import setPageRouteData from "@common/core/lib/route/set-page-data";
  import CPage from "~module/app/component/route";

  const loadPageData = async (pageData, ctx) => {

    try {

      let {data} = await ctx.apolloClient.query({
        query: require('~module/entity/graphql/getPost.gql'),
        fetchPolicy: 'no-cache',
        variables: {
          filter: {
            slug: ctx.route.params.slug,
            type: "sm-hub-post"
          },
          content: true,
          gallery: true,
          termsTaxonomy: ['category', 'post_tag', "sm-hub-term"],
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
      CItem
    },
    data() {
      return {}
    },
    computed: {},
    async preFetch(ctx) {
      return setPageRouteData('static', ctx, loadPageData)
    },
  }
</script>

<style lang="scss" scoped>


</style>
