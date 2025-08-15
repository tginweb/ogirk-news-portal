<template>
  <q-page class="q-mt-xl">

    <div class="container q-mb-lg">

      <QueryView
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

              <query-item-special
                :item="item"
                class="q-ml-auto q-mr-auto bg-white q-mb-xl"
                rowClass=""
                style="max-width:1100px;"
              />

            </div>

          </div>

          <div class="text-center q-mt-lg">
            <q-btn @click="onNavMore" color="primary" size="lg">загрузить еще</q-btn>
          </div>

        </template>

      </QueryView>


    </div>

  </q-page>
</template>

<script>
  import generateQueryInfo from '@common/graphql-apollo/lib/generate-query-info'

  import CPage from '~module/app/layout/main/page/1cols'

  export default {
    name: 'frontpage.feed',
    components: {
      CPage,


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
                tax: [{taxonomy: "sm-role", slug: "frontpage"}],
              },
              nav: {
                limit: 20
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
