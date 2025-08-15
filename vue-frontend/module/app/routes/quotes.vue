<template>
  <q-page class="q-mt-lg q-mb-xl">

    <CPageLayout>
      <template v-slot:header>

        <el-page-header title="Цитаты"/>

      </template>
      <template v-slot:default>


        <query-items-grid
          :item="{
            class: 'col-12',
            is: 'query-item-quote',
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

      <template v-slot:right>
        <CPageSidebar/>
      </template>
    </CPageLayout>

  </q-page>
</template>

<script>
  import CPageLayout from '~module/app/layout/main/page/2cols'
  import CPageSidebar from '~module/app/layout/main/sidebar/common'
  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";
  import CPage from "~module/app/component/route";

  export default {
    name: 'page.quotes',
    extends: CPage,
    components: {
      CPageLayout,
      CPageSidebar
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
                tax: [
                  {taxonomy: "sm-role", slug: "quote"}
                ],
              },
              nav: {
                limit: 20
              },
              imageSize: 't1.5',
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
