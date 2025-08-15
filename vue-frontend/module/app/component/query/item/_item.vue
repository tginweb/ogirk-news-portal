<template>

  <div class="query-item">


  </div>

</template>

<script>
  import CItem from '@common/query/component/item/item'
  import strtr from "locutus/php/strings/strtr";

  const dayjs = require('dayjs')


  export default {
    extends: CItem,
    props: {},
    data() {
      return {

      }
    },
    methods: {

      markupProcess(val) {
        return strtr(val, {
          '#': '<br/>'
        })
      },


    },

    computed: {

      elmMeta() {
        return this.elementParams('meta')
      },

      itemDateFormatted() {

        const d = dayjs(parseInt(this.itemDate)).tz()

        const isYesterday = d.isYesterday()
        const isToday = d.isToday()

        if (!this.elmDate.format || this.elmDate.format === 'near') {

          if (isToday) {
            return this.$util.date.timestampToFormat(this.itemDate, 'сегодня в HH:mm')
          } else if (isYesterday) {
            return this.$util.date.timestampToFormat(this.itemDate, 'вчера в HH:mm')
          } else {
            return this.$util.date.timestampToFormat(this.itemDate, 'DD MMMM YYYY в HH:mm')
          }

        } else {
          return this.$util.date.timestampToFormat(this.itemDate, this.elmDate.format)
        }

      },
    }
  }
</script>

<style lang="scss" scoped>

  .query-item {

  }

</style>
