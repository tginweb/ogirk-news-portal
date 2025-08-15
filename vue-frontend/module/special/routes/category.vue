<template>
  <q-page class="q-mt-lg">

    <CItem
      :item="pageData.entity"
      @loaded="onLoaded"
      v-if="pageData.entity"
    />

  </q-page>
</template>

<script>
  import setPageRouteData from "@common/core/lib/route/set-page-data";
  import CItem from '~module/app/component/query/item/page/term-category/item-special'

  import {trim} from '@common/core/lib/util/base';

  const loadPageData = async (pageData, ctx) => {

    try {
      const slugPath = trim(ctx.route.params.slug, '/').split('/')

      let {data} = await ctx.apolloClient.query({
        query: require('~module/entity/graphql/getTermPage.gql'),
        fetchPolicy: 'no-cache',
        variables: {
          filter: {
            slug: slugPath[slugPath.length - 1],
            taxonomy: "category"
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
    name: `Category`,
    components: {
      CItem,
    },
    created() {

    },
    computed: {

    },
    async preFetch(ctx) {
      return setPageRouteData('static', ctx, loadPageData)
    },
    methods: {
      onLoaded() {

        this.$bus.emit('pageLoaded')

      }
    }
  }
</script>
<style lang="scss" scoped>


</style>
