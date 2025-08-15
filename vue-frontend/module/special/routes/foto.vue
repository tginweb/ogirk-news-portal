<template>
  <q-page class="q-mt-lg">

    <CLayout>
      <template v-slot:header>
        <el-page-header title="Фоторепортажи"/>

      </template>

      <template v-slot:default>
        <query-items-grid
          :item="{
            class: 'col-24',
            is: 'query-item-special',
            elements: {
              title: {
                class: 's-font-md'
              }
            }
          }"
          :items="queries.posts.result.nodes"
          rowClass="q-col-gutter-xl"
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
                type: 'post',
                format: 'gallery',
              },
              nav: {
                limit: 20
              },
              imageSize: 't1.4'
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
    computed: {},

  }
</script>
