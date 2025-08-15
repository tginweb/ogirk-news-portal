<template>
  <q-page class="q-mt-lg q-mb-xl">

    <CPageLayout>
      <template v-slot:default>

        <el-page-header title="Медиа"/>

      </template>
    </CPageLayout>

  </q-page>
</template>

<script>
  import CPageLayout from '~module/app/layout/main/page/1cols'
  import CPageSidebar from '~module/app/layout/main/sidebar/common'
  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";
  import CPage from "~module/app/component/route";

  export default {
    extends: CPage,
    components: {
      CPageLayout,
      CPageSidebar
    },
    apollo: {

      projects: generateQueryInfo('projects', require('~module/entity/graphql/getPosts.gql')),
      landings: generateQueryInfo('landings', require('~module/entity/graphql/getPosts.gql')),

    },
    data() {
      return {
        queries: {
          projects: {
            vars: {
              filter: {
                type: 'sm-hub-post',
                tax: [{taxonomy: "sm-hub-type", slug: "projects"}],
              },
              nav: {
                limit: 12
              },
              imageSize: 't1.5'
            },
            state: {
              isLoading: false
            },
            result: null
          },
          landings: {
            vars: {
              filter: {
                type: 'sm-hub-post',
                tax: [{taxonomy: "sm-hub-type", slug: "projects"}],
              },
              nav: {
                limit: 12
              },
              imageSize: 't1.5'
            },
            state: {
              isLoading: false
            },
            result: null
          },
        },
        calendarDate: null
      }
    },
    computed: {

    },

  }
</script>
