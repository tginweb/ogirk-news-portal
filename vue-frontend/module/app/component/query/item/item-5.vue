<template>
  <div class="query-item" v-bind="bind">

    <div :class="elmContent.class" class="i-content">

      <div :class="elmRow.class" class="i-row row">

        <div :class="elmCol1.class">

          <div class="i-media">

            <div class="i-terms -style1 q-ma-md q-px-xs" v-if="termsOutput.length>0">
              <span :key="term._id" class="i-terms-item" v-for="term of termsOutput.slice(0,1)">
                {{term.name}}
              </span>
            </div>

            <div class="i-thumb">

              <div class="i-thumb__holder">
                <div class="i-thumb__res__holder" v-bind="bindThumbResponsive"></div>
                <div class="i-thumb__res__bimg" v-lazy:background-image="itemImageThumbSrc"/>
              </div>

            </div>

          </div>

        </div>

        <div :class="elmCol2.class">

          <div class="i-info">

            <component v-bind="bindLink">

              <h3 class="i-title q-ma-none q-mb-md" :class="elmTitle.class">
                {{itemTitle}}
              </h3>

              <div class="i-excerpt q-mb-md text-grey-9" v-html="itemExcerptFormatted"/>

            </component>

            <div class="row q-col-gutter-md no-wrap">
              <div class="col col-grow">
                <div class="i-meta">
                  <div class="i-meta-date">
                    {{itemDateFormatted}}
                  </div>
                </div>
              </div>
              <div class="col text-right">

                <div class="i-geo q-gutter-sm" v-if="termsTerritory.length">

                  <router-link
                    :key="term._id"
                    :to="term.url"
                    v-for="term of termsTerritory"
                    class="i-geo-item "
                  >
                    <q-icon name="fas fa-map-marker-alt q-mr-xs" style="vertical-align: baseline;"/>

                    <span>{{term.name}}</span>

                  </router-link>

                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import CItem from './_item-post'

  export default {
    name: 'item-5',
    extends: CItem,
    data() {
      return {
        elementDefaults: {
          row: {
            defclass: 'q-col-gutter-md'
          },
          col1: {
            defclass: 'col-24 col-sm-10'
          },
          col2: {
            defclass: 'col-24 col-sm-14'
          },
          title: {
            defclass: 's-font-xl'
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

  .i-excerpt {
    font-size: 17px;
  }

  .i-info {
    width: 100%;
    padding: 0;
    bottom: 0;
    box-sizing: border-box;
  }

  .i-media {
    color: #FFF;

    a {
      text-decoration: none;
      color: #FFF;
    }
  }

  .i-title {
    font-weight: 700;
    line-height: 1.3em;
  }

  .i-meta-date {
    color: #777;
  }

  .i-geo-item {
    display: inline-block;
    white-space: nowrap;

    span {
      text-decoration:underline;
    }
  }

</style>
