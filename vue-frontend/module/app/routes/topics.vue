<template>
  <q-page class="q-mt-lg q-mb-xl">

    <CLayout>
      <template v-slot:header>
        <el-page-header :title="pageTitle"/>
      </template>

      <template v-slot:default>

        <query-items-grid
          :item="{
            is: 'query-item-topic',
            class: 'col-24 q-mb-lg',
            elements: {

            }
          }"
          :items="queries.posts.result.nodes.slice().sort((x, y) => { return (x.meta.archive === y.meta.archive) ? 0 : x ? 1 : -1 } )"
          class="items overflow-hidden"
          rowClass=""
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
    name: 'page.topics',
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
          title: 'Сюжеты'
        },
        queries: {
          posts: {
            vars: {
              filter: {
                type: 'sm-hub-post',
                tax: [{taxonomy: "sm-hub-type", slug: "topic"}],
              },
              nav: {
                limit: 12
              },
              imageSize: 't1.5',
              termsTaxonomy: ['category', 'post_tag', "sm-hub-term"],
              cache: 'TEMP_LG'
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

  .items {
    /deep/ .i-wrap {
      padding-left: 30px !important;
      padding-right: 30px !important;
    }
  }

</style>
