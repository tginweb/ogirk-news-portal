<template>

  <div class="query-item">

  </div>

</template>

<script>
  import CItem from './_item'

  export default {
    extends: CItem,
    props: {

    },
    data() {
      return {

      }
    },
    methods: {

    },
    computed: {
      itemTitle() {
        return this.item.name
      },
    }
  }
</script>

<style lang="scss" scoped>

  .query-item {

  }

</style>
