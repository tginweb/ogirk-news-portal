<template>
  <q-page class="q-mt-lg q-mb-xl">

    <CPageLayout>
      <template v-slot:default>

        <el-page-header title="Теги"/>

        <router-link
          class="c-item q-mb-lg q-mr-lg s-link text-accent"
          v-for="item of pageData.result.nodes"
          :key="item.nid"
          :to="item.url"
          v-bind="bindItem(item)"
        >

          {{item.name}}

        </router-link>

      </template>
    </CPageLayout>

  </q-page>
</template>

<script>
  import CPageLayout from '~module/app/layout/main/page/1cols'
  import CPageSidebar from '~module/app/layout/main/sidebar/common'
  import setPageRouteData from "@common/core/lib/route/set-page-data";
  import CPage from "~module/app/component/route";

  const loadPageData = async (pageData, ctx) => {

    try {

      let {data} = await ctx.apolloClient.query({
        query: require('~module/entity/graphql/getTerms.gql'),
        fetchPolicy: 'no-cache',
        variables: {
          filter: {
            taxonomy: "post_tag"
          },
          nav: {
            limit: 300,
            sortField: 'lastActivity'
          },
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
    name: 'page.tags',
    extends: CPage,
    components: {
      CPageLayout,
      CPageSidebar
    },
    data() {
      return {
        queries: {
          list: {
            result: null
          },
        },
        calendarDate: null
      }
    },
    computed: {

      minPostCount() {
        return this.pageData.result.nodes.reduce(function (min, item) {
          return ( min < item.postCount ? min : item.postCount );
        });
      },

      maxPostCount() {
        return this.pageData.result.nodes.reduce(function (max, item) {
          return ( max > item.postCount ? max : item.postCount );
        });
      }
    },

    methods: {
      bindItem(item) {
        const res = {
          style: {}
        }

        let imp =  Math.round( item.postCount / 20 )

        if (imp>18) imp = 18;

        res.style.fontSize = (16 + imp) + 'px';

        return res
      }
    },

    async preFetch(ctx) {
      return setPageRouteData('static', ctx, loadPageData)
    },

  }
</script>

<style lang="scss" scoped>

  .c-item {
    display: inline-block;
    border-bottom1: 0;
  }

</style>
