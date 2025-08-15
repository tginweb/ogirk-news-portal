<template>

  <div class="query-items">

    <div v-bind="bindRow">

      <component
        :key="index"
        v-bind="bindItem(item, index)"
        v-for="(item, index) in items"
      />

    </div>

  </div>

</template>

<script>
  import Items from '@common/query/component/items/items'

  export default {
    extends: Items,
    methods: {

    }
  }
</script>

<style lang="scss" scoped>


</style>
