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
        :items="items"
        :regions="[
          {

            outerClass: 'col-24',
            innerClass: 'row q-col-gutter-lg',
            limit: 2,
            item: {
              queryId: queryId,
              is: 'query-item-1',
              class: 'col-24 col-sm-12',
              elements: {
                title: {
                  class: 's-font-lg'
                }
              }
            }
          },
          {
            outerClass: 'col-24',
            innerClass: 'row q-col-gutter-lg',
            limit: secondaryLimit,
            item: {
              queryId: queryId,
              is: 'query-item-3',
              class: 'col-24 col-sm-12',
              rowClass: 'q-col-gutter-md',
              elements: {
                terms: {
                  enable: false
                }
              }
            }
          },
        ]"
        class="row q-col-gutter-lg"
      />
    </template>
  </el-widget>

</template>

<script>
  import CQueryView from '@common/query/component/view/view'

  export default {
    props: {
      secondaryLimit: {default: 8}
    },
    extends: CQueryView,
  }
</script>

<style lang="scss" scoped>


</style>
