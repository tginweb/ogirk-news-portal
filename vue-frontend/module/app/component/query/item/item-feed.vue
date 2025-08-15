<template>
  <div class="query-item" v-bind="bind">
    <div :class="elmContent.class" class="i-content">
      <div :class="elmRow.class" class="i-row row">

        <div class="col-24 col-md-9" v-if="itemImage.src">
          <div class="i-media">
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

        <div :class="{'col-24': true, 'col-md-15': itemImage.src}">

          <div class="i-info q-pa-md q-pa-md-md">

            <component v-bind="bindLink">

              <div class="i-terms -style3 q-mb-md" v-if="itemTerms.length>0">
                <span :key="term._id" class="i-terms-item" v-for="term of itemTerms.slice(0,1)">
                  {{term.name}}
                </span>
              </div>

              <h3 class="i-title q-ma-none q-mb-sm q-mb-md-md" :class="elmTitle.class">
                {{itemTitle}}
              </h3>

              <div class="i-meta">
                <div class="i-meta-date">
                  {{itemDateFormatted}}
                </div>
              </div>

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
    name: 'item-feed',
    extends: CItem,
    data() {
      return {
        elementDefaults: {

          title: {
            defclass: 's-font-md s-font-md-lg'
          },
        }
      }
    },
    methods: {}
  }
</script>

<style lang="scss" scoped>

  .query-item {
    border-radius: 8px;
    border: 1px solid rgba(0,0,0,.1);
    overflow: hidden;
  }

  .i-excerpt {
    font-size: 17px;
  }

  .i-thumb__image {
    border-top-left-radius1: 8px;
    border-bottom-left-radius1: 8px;
    padding1: 1px;
  }

  .i-title {
    font-weight: 700;
    line-height: 1.4em;
  }

  .i-meta-date {
    color: #777;
    font-size: 15px;
  }

</style>
