<template>
  <div class="query-item" v-bind="bind">
    <div :class="elmContent.class" class="i-content">
      <div :class="elmRow.class" class="i-row row">

        <div class="col-24 col-md-9" v-if="itemImage.src">
          <div class="i-media">
            <div class="i-thumb">

              <component v-bind="bindLinkExt">
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

            <component v-bind="bindLinkExt">

              <div class="flex items-center q-mb-sm">

                <div class="i-terms -style3" v-if="itemTerms.length>0">
                  <span :key="term._id" class="i-terms-item" v-for="term of itemTerms.slice(0,1)">
                    {{term.name}}
                  </span>
                </div>

                <div class="i-meta q-ml-auto">
                  <div class="i-meta-date">
                    {{itemDateFormatted}}
                  </div>
                </div>
              </div>

              <h3 class="i-title s-font-md s-font-md-lg  q-ma-none q-mb-sm q-mb-md-sm" v-html="itemTitle"></h3>

              <div class="i-excerpt" v-if="itemExcerptFragments.length">

                <span
                  class="i-excerpt-fragment q-mb-xs"
                  v-for="fragment of itemExcerptFragments"
                  v-html="'... ' + fragment + ' ...'"
                >
                </span>

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
    name: 'item-search',
    extends: CItem,
    data() {
      return {
        elementDefaults: {
          date: {
            format: 'DD MMMM YYYY'
          },
        }
      }
    },
    methods: {},
    computed: {

      bindLinkExt() {
        const res = this.bindLink
        res.target = '_blank'
        return res
      },

      itemTitle() {
        return this.item.highlight && this.item.highlight.post_title ? this.item.highlight.post_title[0] : this.item.title
      },
      itemExcerptFragments() {
        return this.item.highlight && this.item.highlight.post_content ? this.item.highlight.post_content : []
      }
    }
  }
</script>

<style lang="scss" scoped>

  .query-item {
    border-radius: 8px;
    border: 1px solid rgba(0, 0, 0, .1);
    overflow: hidden;
  }

  .i-thumb__image {
    max-width: 100%;
    height: auto;
    border-top-left-radius1: 8px;
    border-bottom-left-radius1: 8px;
    padding1: 1px;
  }

  .i-title {
    font-weight: 700;
    line-height: 1.3em;

    /deep/ {
      em {
        background: #eee;
        padding: 0 5px 0 5px;
        font-style: normal;
      }
    }
  }

  .i-excerpt {
    font-size: 16px;

    .i-excerpt-fragment {

      /deep/ {
        em {
          font-style: normal;
          font-weight: bold;
        }
      }
    }
  }

  .i-meta-date {
    color: #777;
    font-size: 14px;
  }

</style>
