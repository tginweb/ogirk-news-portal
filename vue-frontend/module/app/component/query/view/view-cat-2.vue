<template>

  <el-widget
    :bodyOnly="widget.bodyOnly"
    :loading="loading"
    :moreLabel="widget.moreLabel"
    :moreUrl="widget.moreUrl"
    :skeleton="widget.skeleton"
    :title="widget.title"
  >
    <template v-slot:default>

      <query-items-regional
        :items="query.result.nodes"
        :regions="[
          {
            outerClass: 'col-24 col-sm-12',
            innerClass: 'row',
            limit: 1,
            item: {
              is: 'query-item-5',
              class: 'col-24',
              elements: {
                image: {
                  size: 't1.5'
                },
                row: {
                  class: 'q-col-gutter-md'
                },
                col1: {
                  defclass: 'col-24',
                },
                col2: {
                  defclass: 'col-24',
                },
                excerpt: {
                  limit: 230
                },
              }
            }
          },
          {
            outerClass: 'col-24 col-sm-12',
            innerClass: 'row q-col-gutter-lg',
            limit: 5,
            item: {
              is: 'query-item-3',
              class: 'col-24',
              elements: {
                image: {
                  size: 't1.6'
                },
                terms: {
                  enable: false
                }
              }
            }
          },
        ]"
        class="row q-col-gutter-lg"
        v-if="loaded"
      />
    </template>
  </el-widget>

</template>

<script>
  import CQueryView from '@common/query/component/view/view'

  export default {
    extends: CQueryView,
  }
</script>

<style lang="scss" scoped>


</style>
