<template>
  <q-page class="q-mt-lg q-mb-xl">

    <CLayout>

      <template v-slot:header>

        <el-page-header :showSubmenu="screen.gt.sm" :title="pageTitle">

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


        <div
          v-for="year of [2009,2010,2011,2012,2013,2014,2015,2016,2017,2018,2019,2020,2021].reverse()"
          :key="year"
          class="q-mb-md"
        >

          <div class="q-mr-md q-mb-sm" style="font-size: 23px;">
            {{ year }}
          </div>

          <div class="flex q-gutter-x-md">

            <q-btn
              v-for="month of [1,2,3,4,5,6,7,8,9,10,11,12]"
              v-if="year!==2021 || month<=6"
              :key="month"
              :href="'/archive/'+ year + '-' + (month >= 10 ? month : '0'+month)"
              :label="monthNames[month]"
              class="q-mb-md flex"
              color="primary"
              outline
              type="a"
            />

          </div>

        </div>

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
    news: generateQueryInfo('news', require('~module/entity/graphql/getPosts.gql'), {}, {varPath: 'cqueries.news.vars'}),
  },
  data() {
    return {
      page: {
        title: 'Календарь материалов'
      },
      queries: {

        news: {
          state: {
            isLoading: false
          },
          result: null
        },

      },
      monthNames: {
        1: 'Январь',
        2: 'Февраль',
        3: 'Март',
        4: 'Апрель',
        5: 'Май',
        6: 'Июнь',
        7: 'Июль',
        8: 'Август',
        9: 'Сентябрь',
        10: 'Октябрь',
        11: 'Ноябрь',
        12: 'Декабрь',
      }

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
