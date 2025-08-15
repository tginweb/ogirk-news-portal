<template>

  <div class="query-item ">

    <div class="i-content q-pa-lg bg-white">

      <div class="q-gutter-md">
        <component
          :data="element"
          :is="'el-content-builder-' + element.acf_fc_layout"
          :key="index"
          v-for="(element, index) of builderElements"
          class="s-font-lg "
        />
      </div>

    </div>

  </div>

</template>

<script>

  import CItem from './_item-post'

  export default {
    name: 'item-note',
    extends: CItem,
    data() {
      return {}
    },
    methods: {},
    computed: {
      builderElements() {
        return this.item.meta.content_builder
      }
    }
  }

</script>

<style lang="scss" scoped>

  .i-content {
    box-shadow: inset 0 0 0 1px rgba(0, 0, 0, .1);
    border-radius: 8px;
  }

</style>
