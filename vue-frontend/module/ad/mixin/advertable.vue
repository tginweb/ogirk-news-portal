<template>
  <div>

  </div>
</template>

<script>

  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";

  export default {
    components: {},
    apollo: {
      ads: generateQueryInfo('ads', require('~module/entity/graphql/getPosts.gql'), {}, {
        varPath: 'queryAds.vars',
        resPath: 'queryAds.result'
      }),
    },
    data() {
      return {
        queryAds: {
          vars: {
            filter: {
              type: 'sm-ad-item',
              tax: [{
                taxonomy: "sm-ad-zone", ids: [
                  6214,
                  6215,
                  6536,
                  7838,
                  6215,
                  7839,
                  7159
                ]
              }],
            },
            nav: {
              limit: 100,
              sortField: "menuOrder",
              sortAscending: true
            },
            termsTaxonomy: ['sm-ad-zone'],
            cache: 'TEMP_MD'
          },
          state: {isLoading: false},
          result: null
        },
      }
    },
    methods: {},
    computed: {

      ads() {
        return this.queryAds.result && this.queryAds.result.nodes
      },

      adsByZoneSlug() {
        return (this.ads || []).reduce((map, obj) => {
          if (obj.terms.length) {
            obj.terms.forEach((zoneTerm) => {
              map[zoneTerm.slug] = map[zoneTerm.slug] || []
              map[zoneTerm.slug].push(obj)
            })
          }
          return map
        }, {});
      },

    }
  }
</script>

<style lang="scss" scoped>


</style>
