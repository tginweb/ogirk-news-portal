<template>

  <q-dialog maximized position="top" v-model="visible">

    <div ref="dailogContent" style="width: 1200px; max-width: 90vw; background: transparent;box-shadow:none;">

      <div class="flex">
        <q-space/>
        <q-btn color="white" dense flat icon="close" round size="30px" style="" v-close-popup/>
      </div>

      <q-input
        autofocus
        bg-color=""
        class="c-query bg-white"
        input-class="text-center c-query-input"
        placeholder="введите запрос"
        v-model="queryModel"
      />

      <div class="q-mt-lg" v-if="queries.list.result">

        <div class="c-query-mode flex q-mt-md q-mb-md">

          <div class="">

            <q-btn-toggle
              :options="[
              {label: 'по дате', value: 'post_date'},
              {label: 'по релевантности', value: '_score'},
            ]"
              color="white"
              size="md"
              text-color="grey-9"
              toggle-color="primary"
              v-model="sortBy"

            />

          </div>

          <div class="q-ml-auto">
            <router-link class="text-white" to="/search">расширенный поиск</router-link>
          </div>

        </div>

        <div
          :key="item._id"
          v-for="item of nodes"
        >

          <div class="flex q-mb-lg">

            <query-item-search
              :item="item"
              class="q-ml-auto q-mr-auto bg-white"
              rowClass="q-col-gutter-md"
              style="max-width:900px;"
            />

          </div>

        </div>

      </div>

    </div>

  </q-dialog>

</template>

<script>
  import {debounce} from '@common/core/lib/util/base';

  import CDialog from '@common/dialog/component/dialog'
  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";

  export default {
    extends: CDialog,
    props: {
      model: {}
    },
    apollo: {
      list: generateQueryInfo('list', require('~module/entity/graphql/getPostsElastic.gql'), {
        skip: true,
      }, {
        varPath: 'cqueries.list.vars',
      }),
    },
    components: {},
    data() {
      return {
        dialogModule: 'app',
        dialogName: 'search',
        queryModel: '',
        query: '',
        queryDebounce: null,

        queries: {
          list: {
            result: null,
            state: {isLoading: false, mode: null},
          },
        },

        sortBy: 'post_date'
      }
    },
    computed: {
      cqueries() {
        return {
          list: {
            vars: {
              filter: {
                type: 'post',
                phrase: this.query
              },
              nav: {
                limit: 70,
                next: null,
                sortField: this.sortBy
              },
              imageSize: 'm1.78'
            },
          },
        }
      },

      nodes() {

        const highlightsById = this.queries.list.result.pageInfo.highlights.reduce((map, item) => (map[item._id] = item, map), {});

        return this.queries.list.result.nodes.map((node) => {
          return {
            ...node,
            highlight: highlightsById[node._id].fragments
          }
        })
      }
    },
    created() {
      this.queryDebounce = debounce(() => {

        this.query = this.queryModel

        this.queries.list.state.mode = null

        if (!this.query) {
          this.queries.list.result = null
          this.$apollo.queries.list.skip = true
        } else {
          this.$apollo.queries.list.skip = false
        }

      }, 1000)
    },
    methods: {},
    watch: {
      queryModel() {
        this.queryDebounce()
      }
    },
  }

</script>

<style lang="scss">

  .c-query {
    font-size: 40px;
  }

  .c-query-input {

  }

  .c-query-mode {

  }

</style>
