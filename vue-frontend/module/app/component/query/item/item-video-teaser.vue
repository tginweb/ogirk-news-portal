<template>
  <div class="query-item" v-bind="bind">
    <div :class="elmContent.class" class="i-content">
      <div :class="elmRow.class" class="i-row row">
        <div :class="elmCol1.class">
          <div class="i-media ">

            <div class="i-format">
              <q-icon size="12px" :name="$icons.fasPlay" class=""/>
            </div>

            <div class="i-thumb">
              <component v-bind="bindLink">
                <img
                  v-lazy="itemImageThumbSrc"
                  class="i-thumb__image"
                />
              </component>
            </div>
          </div>
        </div>
        <div :class="elmCol2.class">
          <div class="i-info">
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
    name: 'item-video-teaser',
    extends: CItem,
    data() {
      return {
        elementDefaults: {
          row: {
            defclass: 'q-col-gutter-x-md'
          },
          col1: {
            defclass: 'col-8'
          },
          col2: {
            defclass: 'col-16 q-pt-sm'
          },
          title: {
            defclass: 's-font-md'
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

  .i-title {
    font-weight: 600;
    line-height: 1.3em;
  }

  .i-format {
    margin: auto;
    position: absolute;
    top: 50%; left: 50%;
    -webkit-transform: translate(-50%,-50%);
    -ms-transform: translate(-50%,-50%);
    transform: translate(-50%,-50%);
    background-color: rgba(30,30,30, 0.5);
    padding: 0px 6px;
    color: #fff;
    border: 1px solid;

  }

</style>
