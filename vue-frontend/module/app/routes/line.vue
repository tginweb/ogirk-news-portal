<template>

  <q-page class="q-mt-lg q-mb-xl">

    <CLayout>

      <template v-slot:top>

        <el-page-header title="Прямая линия с губернатором Игорем Кобзевым"/>

        <br>

        <p><b>27 декабря в 19:00 до 20:30</b> пройдет Прямая линия с губернатором Иркутской области Игорем Кобзевым.</p>

        <p>Трансляцию можно посмотреть на следующих информационных площадках: на телеканале «Россия 24», в телевизионном эфире на телеканалах ТРК Братск, Усть-Кут ТК «Диалог» и Усть-Илимск телеканал ИРТ. На сайтах и в социальных сетях  телеканалов «Россия 24»,  «АИСТ ТВ», «Вести Иркутск», «ИРТ», «БСТ» и «Актис», телерадиокомпании «Диалог», информационных порталов  «IRK.ru», «Тайшет24»,  газеты «Областная».</p>

      </template>

      <template v-slot:default>


        <iframe width="100%" height="500" src="https://www.youtube.com/embed/oENY5ZKZnUo" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

      </template>

      <template v-slot:right>


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

    data() {
      return {
        page: {
          title: 'Прямая линия с губернатором Игорем Кобзевым'
        },
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
