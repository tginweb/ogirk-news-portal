<template>
  <q-page class="q-mt-lg">

    <CLayout>
      <template v-slot:header>
        <el-page-header title="Сюжеты"/>
      </template>

      <template v-slot:default>
        <query-items-grid
          :item="{
            class: 'col-24',
            is: 'query-item-special'
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

  export default {
    components: {
      CLayout,
    },
    apollo: {
      posts: generateQueryInfo('posts', require('~module/entity/graphql/getPosts.gql')),
    },
    data() {
      return {
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
