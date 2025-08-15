<template>

  <div @mouseleave="hover=false" @mouseover="hover=true" class="query-item" v-bind="bind">

    <div :class="elmContent.class" class="i-content">

      <query-item-ad-teaser-demo :demo="demo" v-if="demo"/>

      <div class="i-media">

        <div class="i-thumb">

          <component v-bind="bindLink">

            <div class="i-thumb__holder">
              <div class="i-thumb__res__holder bg-grey-3" v-bind="bindThumbResponsive"></div>
              <div class="i-thumb__res__bimg" v-lazy:background-image="itemImageThumbSrc"/>
            </div>

          </component>

        </div>


        <div class="i-info q-pb-md s-overlay">

          <component class="block q-pt-lg q-px-md" v-bind="bindLink">

            <h3
              :class="elmTitle.class"
              class="i-title q-ma-none"
              v-if="itemTitle"
            >
              {{itemTitle}}
            </h3>

          </component>

          <transition name="slide-fade">

            <div class="q-mt-xs" v-if="hover || !expandable">

              <component class="block q-px-md" v-bind="bindLink">

                <h4 :class="elmSubtitle.class" class="i-subtitle q-ma-none q-mt-sm">
                  {{item.subtitle}}
                </h4>

              </component>

            </div>

          </transition>

        </div>

      </div>

    </div>

  </div>

</template>

<script>
  import CItem from './_item-post'
  import CItemMeta from './meta/meta'
  import CShare from './meta/share-dropdown'

  export default {
    name: 'item-7',
    extends: CItem,
    components: {
      CItemMeta,
      CShare
    },
    data() {
      return {
        hover: false,
        elementDefaults: {
          terms: {
            enable: true
          },
          title: {
            defclass: 's-font-lg'
          },
          subtitle: {
            defclass: 's-font-sm'
          },
          format: {
            enable: true
          }
        }
      }
    },
    methods: {}
  }
</script>

<style lang="scss" scoped>

  .slide-fade-enter-active {
    transition: all 0.3s ease;
  }

  .slide-fade-leave-active {
    transition: all .1s ease;
  }

  .slide-fade-enter, .slide-fade-leave-to {
    margin-bottom: -20px;
    opacity: 0;
  }

  .query-item {
    color: #FFF;

    a {
      text-decoration: none;
      color: #FFF;
    }
  }

  .i-content {
    border-radius: 4px;
    overflow: hidden;
    position: relative;;
  }

  .i-info {
    width: 100%;
    position: absolute;
    bottom: 0;
    box-sizing: border-box;
    text-shadow: 0 0 2px rgba(0, 0, 0, 0.5);
  }

  .i-title {
    line-height: 1.3em;
    font-weight: 700;

    a {
      display: block;;
    }
  }

  .i-subtitle {
    line-height: 1.3em;
    font-weight: 700;

    a {
      display: block;;
    }
  }

  .i-meta {
    color: #a1a1a1;
    font-size: 13px;
    text-shadow: 0 0 2px rgba(0, 0, 0, 0.5);
  }


</style>
