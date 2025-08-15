<template>
  <q-page class="q-mt-lg q-mb-xl">

    <CPageLayout v-if="pageData && pageData.entity">
      <template v-slot:header>

        <el-page-header :title="pageTitle"/>

      </template>
      <template v-slot:default>

        <div class="c-body">

          <template v-if="pageData.entity.builderType === 'gutenberg'">

            <wp-block-blocks
              :children="pageData.entity.builder"
              class="sm-content-style"
            />

          </template>
          <template v-else>

            <div
              class="sm-content-style"
              v-html="pageData.entity.contentFormatted"
            />

          </template>

        </div>

      </template>
    </CPageLayout>

  </q-page>
</template>

<script>
  import CPageLayout from '~module/app/layout/main/page/1cols'
  import CPageSidebar from '~module/app/layout/main/sidebar/common'
  import setPageRouteData from "@common/core/lib/route/set-page-data";
  import CPage from "~module/app/component/route";

  import {trim} from '@common/core/lib/util/base';

  const loadPageData = async (pageData, ctx) => {

    try {
      let {data} = await ctx.apolloClient.query({
        query: require('~module/entity/graphql/getPost.gql'),
        fetchPolicy: 'no-cache',
        variables: {
          filter: {
            type: 'page',
            codePath: trim(ctx.route.params.code, '/')
          },
          content: true,
          gallery: true,
          view: 'public_full'
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
      CPageLayout,
      CPageSidebar
    },
    apollo: {},
    data() {
      return {}
    },
    async preFetch(ctx) {
      return setPageRouteData('static', ctx, loadPageData)
    },
    computed: {},
  }
</script>
