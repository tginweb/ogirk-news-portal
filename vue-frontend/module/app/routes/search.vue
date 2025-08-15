<template>

  <q-page class="q-mt-lg q-mb-xl">

    <CLayout>

      <template v-slot:top>

        <el-page-header title="Поиск"/>

        <q-input
          autofocus
          bg-color=""
          class="c-query bg-white"
          input-class="text-center c-query-input"
          placeholder="введите запрос"
          v-model="queryModel"
        />

      </template>

      <template v-slot:default>

        <div class="" v-if="queries.list.result">

          <div class="c-query-mode flex q-mb-lg">

            <div class="q-ml-auto">

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

      </template>

      <template v-slot:right>

        фильтр по критериям рубрика, автор

      </template>

    </CLayout>

  </q-page>

</template>

<script>
  import CLayout from '~module/app/layout/main/page/2cols'
  import {debounce} from '@common/core/lib/util/base'
  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";
  import CPage from "~module/app/component/route";

  export default {
    extends: CPage,
    components: {
      CLayout
    },
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
    data() {
      return {
        dialogId: 'app/search',
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
                limit: 500,
                next: null,
                sortField: this.sortBy
              },
              imageSize: 't1.5'
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
