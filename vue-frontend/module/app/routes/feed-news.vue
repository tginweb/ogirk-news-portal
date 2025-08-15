<template>
  <q-page class="q-mt-lg q-mb-xl">

    <CLayout>

      <template v-slot:header>

        <el-page-header title="Новости"/>

      </template>
      <template v-slot:default>

        <query-view
          :query="queries.news"
          :queryHandler="()=>$apollo.queries.news"
          :queryResult.sync="queries.news.result"
          :queryState.sync="queries.news.state"
        >

          <template v-slot:default="{items, onNavMore}">

            <div
              :key="item._id"
              v-for="item of items"
            >

              <div class="flex q-mb-md">

                <query-item-feed
                  :item="item"
                  class="q-ml-auto q-mr-auto bg-white"
                  rowClass=""
                  style="max-width:800px;width: 800px;"
                />

              </div>

            </div>

            <div class="text-center q-mt-lg">
              <q-btn @click="onNavMore" color="primary" size="lg">загрузить еще</q-btn>
            </div>

          </template>


        </query-view>

      </template>
    </CLayout>


  </q-page>
</template>

<script>
  import CLayout from '~module/app/layout/main/page/1cols'
  import generateQueryInfo from '@common/graphql-apollo/lib/generate-query-info'
  import CPage from "~module/app/component/route";

  export default {
    name: 'frontpage.feed',
    extends: CPage,
    components: {
      CLayout,
    },
    apollo: {
      news: generateQueryInfo('news', require('~module/entity/graphql/getPosts.gql'), {}),
    },
    data() {
      return {
        queries: {

          news: {
            vars: {
              filter: {
                type: 'post',
                tax: [{taxonomy: "sm-role", slug: "news"}],
              },
              nav: {
                limit: 40
              },
              imageSize: 't1.5',
              imageHook: false
            },
            state: {
              isLoading: false
            },
            result: null
          },

        },

      }
    },
    methods: {},
    created() {

    },
    computed: {}
  }
</script>
