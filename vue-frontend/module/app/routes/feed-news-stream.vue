<template>
  <q-page class="q-mt-lg q-mb-xl">

    <CLayout>

      <template v-slot:header>

        <el-page-header title="Прослушать новости"/>

      </template>
      <template v-slot:default>

        <q-btn
          @click="$store.dispatch('dialogShow', ['app/stream', {}])"
          color="red-5"
          dense
          icon="keyboard_voice"
          label="прослушать новости"
          size="xl"
          outline
        />

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
