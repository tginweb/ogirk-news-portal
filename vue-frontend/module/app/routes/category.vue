<template>
  <q-page class="q-mt-lg q-mb-xl">

    <component
      :is="component"
      :item="pageRouteData.entity"
      @loaded="onLoaded"
      v-if="pageRouteData.entity && pageRouteData.entity.slug !== 'bez-rubriki'"
    />

  </q-page>
</template>

<script>
  import setPageRouteData from "@common/core/lib/route/set-page-data";
  import CItemPrimary from '~module/app/component/query/item/page/term-category/item-primary'
  import CItemPrimaryTerritory from '~module/app/component/query/item/page/term-category/item-primary-territory'
  import CItemSecondary from '~module/app/component/query/item/page/term-category/item-secondary'
  import CItemTerritory from '~module/app/component/query/item/page/term-category/item-territory'
  import {trim} from '@common/core/lib/util/base'

  import CPage from '~module/app/component/route'


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
    name: 'page.category',
    extends: CPage,
    components: {
      CItemPrimary,
      CItemSecondary,
      CItemTerritory,
      CItemPrimaryTerritory
    },
    created() {

    },
    computed: {
      component() {
        if (this.pageRouteData.entity.parent > 0) {
          return this.pageRouteData.entity.parent === 1443 ?  'c-item-territory' : 'c-item-secondary'
        } else {
          return this.pageRouteData.entity.nid === 1443 ? 'c-item-primary-territory' : 'c-item-primary'
        }
      }
    },
    methods: {
      onLoaded() {

        console.log('LOADED')
        this.$bus.emit('pageLoaded')

      }
    },
    async preFetch(ctx) {
      return setPageRouteData('static', ctx, loadPageData)
    },
  }
</script>
<style lang="scss" scoped>


</style>
