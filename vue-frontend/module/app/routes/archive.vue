<template>
  <q-page class="q-mt-lg q-mb-xl">

    <CLayout>

      <template v-slot:header>

        <el-page-header :title="pageTitle" :showSubmenu="screen.gt.sm">

          <template v-slot:default>
            <el-page-header :title="pageTitle"/>
          </template>

          <template v-slot:side>

            <CCalendarDropdown
              :date="$route.params.date"
              :filter="calendarFilter"
              :url="'/archive/$date'"
            />

          </template>

        </el-page-header>

      </template>
      <template v-slot:default>


        <query-view
          :pager="{desktopType: 'prevnext', mobileType: 'loadmore', align: 'center'}"
          :queryHandler="()=>$apollo.queries.news"
          :queryResult.sync="queries.news.result"
          :queryState.sync="queries.news.state"
        >
          <template v-slot:default="{items, state, loading, onNavMore}">

            <query-items-grid
              :item="{
                  class: 'col-24 col-md-12 q-mt-lg',
                  is: 'query-item-5',
                  elements: {
                    content: {
                      class: ''
                    },
                    row: {
                      class: 'q-col-gutter-md'
                    }
                  }
                }"
              :items="items"
              rowClass="q-col-gutter-lg"
              v-if="!loading"
            />
            <el-skeleton v-else/>

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
  import MRoutable from "~module/app/mixin/routable";
  import CCalendarDropdown from '~module/app/component/query/filter/calendar/dropdown'

  export default {
    name: 'frontpage.feed.all',
    extends: CPage,
    mixins: [MRoutable],
    components: {
      CLayout,
      CCalendarDropdown
    },
    apollo: {
      news: generateQueryInfo('news', require('~module/entity/graphql/getPosts.gql'), {},{varPath: 'cqueries.news.vars'}),
    },
    data() {
      return {
        page: {
          title: 'Архив материалов'
        },
        queries: {

          news: {
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
    computed: {
      calendarFilter() {
        return {
          type: 'post',
        }
      },
      cqueries() {
        return {
          news: {
            vars: {
              filter: {
                type: 'post',
                ...this.routeFilter,
              },
              nav: {
                limit: 5,
                ...this.routeNav,
              },
              imageSize: 't1.5',
              cache: 'TEMP_LG'
            },
          },
        }
      },

    }
  }
</script>
