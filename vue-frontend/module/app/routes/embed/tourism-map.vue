<template>
  <q-page class="">

      <CQueryViewRegionsMap
        :query="queries.regions"
        :widget="{title: 'Территории', moreUrl: '/category/territory', moreLabel: 'в раздел'}"
        height="600px"
      />

  </q-page>
</template>

<script>
  import generateQueryInfo from '@common/graphql-apollo/lib/generate-query-info'
  import generatePageQueryInfo from '~module/app/lib/apollo-graphql/generate-page-query-info'

  import CQueryViewRegionsMap from "~module/app/component/query/view/view-regions-map-tourism";

  import CPage from "~module/app/component/route";

  const CACHE_DEF = 'TEMP_MD';

  export default {
    name: 'page.tourism.embed.map',
    extends: CPage,
    components: {
      CQueryViewRegionsMap
    },
    apollo: {
      regions: generatePageQueryInfo('regions', require('~module/entity/graphql/getTerms.gql'), {}),
    },
    data() {
      return {
        page: {
          title: 'Официальные новости Иркутской области: общество, политика, экономика, территории'
        },
        pageQueriesLoading: 0,
        pageLoaded: false,

        queries: {

          regions: {
            vars: {
              filter: {
                taxonomy: "category",
                parent: 1443
              },
              nav: {
                limit: 300,
                sortField: 'name',
                sortAscending: true
              },
              field: true,
              view: 'public_full',
              posts: true,
              postNav: {limit: 6},
              cache: CACHE_DEF
            },
            state: {
              isLoading: false,
              skip: true
            },
            result: null
          },
        },
        socnetsLoaded: false,
        isMounted: false
      }
    },

    methods: {},
    created() {

    },
    mounted() {
      this.isMounted = true
      this.queries.regions.state.skip = false
    },
    computed: {},
    watch: {}
  }
</script>

<style lang="scss" scoped>

  .turizm {
    color: #fff;

    a {
      color: #fff;
    }
  }

  .turizm__main__title {
    font-weight: 800;
    line-height: 1.3em;
    text-decoration: none;
    display: block;
  }

  .turizm__main__more {
    font-size: 25px;
    border: 1px solid #FFF;
    display: inline-block;
    text-decoration: none;
    line-height: 1;
    border-radius: 8px;
  }

  .turizm__block__header {
    font-size: 25px;
    line-height: 1.2em;
    font-weight: 800;
  }

  .turizm__block__items {
    font-size: 19px;
    padding: 0;
    margin: 0;
    list-style-type: none;

    li {
      &:not(:last-child) {
        margin: 0 0 0.1em 0;
      }

      a {
        text-decoration: none;
      }
    }
  }


</style>


