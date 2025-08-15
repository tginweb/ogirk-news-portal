<template>
  <div class="query-item" v-bind="bind">

    <div :class="elmRow.class" class="i-row row">

      <div :class="elmCol1.class" class="i-col1">

        <div :class="elmIndex.class" class="i-index">
          {{index}}
        </div>

      </div>
      <div :class="elmCol2.class" class="i-col2">

        <component v-bind="bindLink">

          <div class="i-info">

            <div :class="elements.title && elements.title.class" class="i-title q-ma-none">
              {{itemTitle}}
            </div>

          </div>

        </component>

      </div>

    </div>
  </div>
</template>

<script>
  import CItem from './_item-post'

  export default {
    name: 'item-index',
    extends: CItem,
    data() {
      return {
        elementDefaults: {
          row: {
            defclass: 'no-wrap'
          },
          col1: {
            defclass: 'col col-shrink text-center',
          },
          col2: {
            defclass: 'col'
          },
          title: {
            defclass: ''
          },
          excerpt: {
            limit: 200
          },
          index: {
            defclass: 'q-px-md q-py-xs'
          },
        }
      }
    },
    computed: {
      elmIndex() {
        return this.elementParams('index')
      },
    }
  }
</script>

<style lang="scss" scoped>

  .i-index {
    font-size: 40px;
    line-height: 1em;
    color: #707070;
    text-align: center;
    font-weight: 700;
  }
</style>
