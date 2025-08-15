<template>
  <q-page class="q-mt-lg q-mb-xl">

    <div v-if="pageData.entity">

      <div class="q-mb-md" v-if="termsIsPolis">
        <a href="/mastergid/">
          <img src="/statics/plv.jpg" style="width: 100%;"/>
        </a>
      </div>

      <CItemDesant :irkipedia="pageData.irkipedia" :item="pageData.entity" v-if="pageData.entity.slug==='severnyj-desant'"/>
      <CItem :irkipedia="pageData.irkipedia" :item="pageData.entity" v-else/>

    </div>

  </q-page>
</template>

<script>
  import CItem from '~module/app/component/query/item/page/term-tag/item'
  import CItemDesant from '~module/app/component/query/item/page/term-tag/item-desant'

  import setPageRouteData from "@common/core/lib/route/set-page-data";
  import CPage from "~module/app/component/route";

  const loadPageData = async (pageData, ctx) => {

    try {

      let {data} = await ctx.apolloClient.query({
        query: require('~module/entity/graphql/getTermPage.gql'),
        fetchPolicy: 'no-cache',
        variables: {
          filter: {
            slug: ctx.route.params.slug,
            taxonomy: "post_tag"
          }
        },
      })

      const ipdReqParams = {}

      const entity = data.res;

      if (entity.meta.irkipedia_code && 0) {
        ipdReqParams.find_code = entity.meta.irkipedia_code
      } else {
        ipdReqParams.find_name = entity.name
      }

      try {
        const {data} = await ctx.axios.get('http://irkipedia.ru/ext-term-teaser', {
          params: ipdReqParams
        })

        pageData.irkipedia = data

      } catch (e) {

      }

      pageData.entity = entity
    } catch (e) {

      console.log(e)
    }

    return pageData;
  }

  export default {
    extends: CPage,
    components: {
      CItem,
      CItemDesant
    },
    data() {
      return {}
    },
    computed: {
      termsIsPolis() {
        return this.pageData.entity && ([8041, 8092, 8094, 8095, 8093, 8091, 8090].indexOf(parseInt(this.pageData.entity.nid)) > -1)
      }
    },
    async preFetch(ctx) {
      return setPageRouteData('static', ctx, loadPageData)
    },
  }
</script>

<style lang="scss" scoped>

  .i-about {
    position: relative;
    background: #FFF;
    display: flex;
    max-width: 900px;
    border-radius: 5px;
    font-size: 18px;
  }

</style>
