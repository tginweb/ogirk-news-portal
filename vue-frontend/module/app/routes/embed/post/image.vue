<template>

  <div>
    <component
      :is="component"
      :item="pageData.entity"
      v-if="pageData.entity"
      class="c-embed"
    />
  </div>

</template>

<script>
  import setPageRouteData from "@common/core/lib/route/set-page-data";

  import ItemPostPost from '~module/app/component/query/item/embed/post-post'
  import ItemPostSmQuiz from '~module/app/component/query/item/embed/post-sm-quiz'

  import CPage from "~module/app/component/route";

  const loadPageData = async (pageData, ctx) => {

    try {
      let {data} = await ctx.apolloClient.query({
        query: require('~module/entity/graphql/getPost.gql'),
        fetchPolicy: 'no-cache',
        variables: {
          filter: {
            nid: ctx.route.params.id,
          },
          imageSize: 't1.5'
        },
      })
      pageData.entity = data.res
    } catch (e) {
    }

    return pageData;
  }

  export default {
    extends: CPage,
    components: {
      ItemPostPost,
      ItemPostSmQuiz
    },
    computed: {
      component() {
        if (this.pageData.entity) {
          return 'item-post-' + this.pageData.entity.type
        }
      }
    },
    async preFetch(ctx) {
      return setPageRouteData('static', ctx, loadPageData)
    },
  }
</script>


<style lang="scss" scoped>

  .c-embed {
    max-width: 600px;
  }

</style>
