<template>
  <div class="query-item" v-bind="bind">
    <div :class="elmContent.class" class="i-content">
      <div :class="elmRow.class" class="i-row row">
        <div :class="elmCol1.class">
          <div class="i-media">
            <div class="i-thumb">
              <component v-bind="bindLink">

                <div class="i-thumb__holder">
                  <div class="i-thumb__res__holder" v-bind="bindThumbResponsive"></div>
                  <div class="i-thumb__res__bimg" v-lazy:background-image="itemImageThumbSrc"/>
                </div>

              </component>
            </div>
          </div>
        </div>
        <div :class="elmCol2.class">
          <div class="i-info">

            <div class="i-terms -style3" :class="elmTerms.class" v-if="elmTerms.enable && itemTerms.length">
              <span :key="term._id" class="i-terms-item" v-for="term of itemTerms.slice(0,1)">
                {{term.name}}
              </span>
            </div>

            <component v-bind="bindLink">
              <h3 class="i-title q-ma-none" :class="elmTitle.class">
                {{itemTitle}}
              </h3>
            </component>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import CItem from './_item-post'

  export default {
    name: 'item-3',
    extends: CItem,
    data() {
      return {
        elementDefaults: {
          row: {
            defclass: 'q-col-gutter-md'
          },
          col1: {
            defclass: 'col-8'
          },
          col2: {
            defclass: 'col-16'
          },
          title: {
            defclass: 's-font-md'
          },
          terms: {
            enable: false,
            defclass: 'q-mb-sm',
          },
          excerpt: {
            limit: 200
          },
        }
      }
    },
    methods: {}
  }
</script>

<style lang="scss" scoped>

  .query-item {

    &:first-child {
      .i-content {
        padding-top: 0;
      }
    }

    &:last-child {
      .i-content {
        padding-bottom: 0;
        border-bottom: 0;
      }
    }
  }

  .i-info {
    width: 100%;
    padding: 0;
    bottom: 0;
    box-sizing: border-box;
  }

  .i-title {
    font-weight: 400;
    line-height: 1.3em;
  }

</style>
