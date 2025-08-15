<template>
  <q-page class="q-mt-lg q-mb-xl">

    <CLayout>
      <template v-slot:header>
        <el-page-header :title="pageTitle"/>
      </template>

      <template v-slot:default>
        <query-items-grid
          :item="{
            is: 'query-item-1',
            class: 'col-8',
            elements: {

            }
          }"
          :items="queries.posts.result.nodes"
          class="c-item"
          rowClass="q-col-gutter-md"
          v-if="queries.posts.result"
        />
      </template>
    </CLayout>

  </q-page>
</template>

<script>
  import CLayout from '~module/app/layout/main/page/1cols'
  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";
  import CPage from "~module/app/component/route";

  export default {
    extends: CPage,
    components: {
      CLayout,
    },
    apollo: {
      posts: generateQueryInfo('posts', require('~module/entity/graphql/getPosts.gql')),
    },
    data() {
      return {
        page: {
          title: 'Квизы'
        },
        queries: {
          posts: {
            vars: {
              filter: {
                type: 'sm-quiz',
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
      }
    },
    computed: {},

  }
</script>
<style lang="scss" scoped>

  .c-item {
    /deep/ .i-wrap {
      padding: 10px 20px;
    }
  }

</style>
