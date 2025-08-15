<template>

  <div class="com container q-mt-md " style="padding-left1:0; padding-right1:0">

    <div :class="{'no-wrap': screen.gt.md}" class="row  items-center q-col-gutter-md">

      <query-view-headnews
        :items="queries.ltNews.result.nodes"
        class="col-24 col-sm-16 col-md-18 col-lg-19"
        v-if="queries.ltNews.result"
      />

      <router-view class="col-24 col-sm-8 col-md-6 col-lg-5" name="headline-side"/>

    </div>

  </div>

</template>

<script>
  import QueryViewHeadnews from '~module/app/component/query/view/view-headnews'

  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";

  export default {
    components: {
      QueryViewHeadnews
    },
    apollo: {
      ltNews: generateQueryInfo('ltNews', require('~module/entity/graphql/getPosts.gql'), {}),
    },
    data() {
      return {
        queries: {
          ltNews: {
            vars: {
              filter: {
                type: 'post',
                tax: [
                  {taxonomy: "sm-role", slug: "news"},
                ],
              },
              nav: {
                limit: 4
              },
              termsTaxonomy: ['category', 'sm-role'],
              cache: 'TEMP_LG'
            },
            result: null
          },
        }
      }
    },
  }
</script>

<style lang="scss" scoped>

</style>
