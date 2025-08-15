<template>

  <div class="com">


    <q-date
      :options="calendarOptions"
      emit-immediately
      mask="YYYY-MM-DD"
      minimal
      v-model="calendarDate"
    />

  </div>

</template>

<script>

  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";

  export default {
    props: {
      date: {},
      filter: {},
      url: {}
    },
    apollo: {
      groups: generateQueryInfo('top', require('~module/entity/graphql/getPostsCalendar.gql'), {}, {
        varPath: 'cqueries.groups.vars',
        resPath: 'queries.groups.result',
      }),
    },
    data() {
      return {
        calendarDate: this.getDateStr(this.date),
        queries: {
          groups: {
            result: null
          },
        },
      }
    },
    methods: {

      getDateData(value) {

        let year, month, day, today = new Date();

        if (value) {
          [year, month, day] = value.split('-')
          if (!month) month = 1;
          if (!day) day = 1;
        } else {
          year = today.getFullYear();
          month = today.getMonth() + 1;
          day = today.getDate();
        }

        return {
          year: parseInt(year),
          month: parseInt(month),
          day: parseInt(day)
        }
      },

      getDateStr(value) {
        const date = this.getDateData(value)
        return date.year + '-' + this.monthToStr(date.month) + '-' + this.dayToStr(date.day)
      },

      dayToStr(v) {
        return v > 9 ? '' + v : '0' + v + ''
      },

      monthToStr(v) {
        return v > 9 ? '' + v : '0' + v
      },
    },
    computed: {

      calendarOptions() {

        if (this.queries.groups.result)
          return this.queries.groups.result.map(item => {
            return item.info.year + '/' + this.monthToStr(item.info.month) + '/' + this.dayToStr(item.info.day)
          })

        return []
      },

      dateYM() {
        const data = this.getDateData(this.calendarDate)
        return {
          year: data.year,
          month: data.month,
        }
      },

      varsFilter() {

        return {
          ...this.filter,
          ...this.dateYM,
        }
      },

      cqueries() {
        return {
          groups: {
            vars: {
              filter: this.varsFilter
            }
          },
        }
      }
    },

    watch: {
      calendarDate(date, prevDate) {

        const dateData = this.getDateData(date)
        const prevDateData = this.getDateData(prevDate)

        if (dateData.year !== prevDateData.year || dateData.month !== prevDateData.month)
          return;


        const path = this.url.replace(/\$date/, date)
        this.$router.push(path).catch((e) => {

        })
      }
    }
  }

</script>

<style lang="scss" scoped>


</style>
