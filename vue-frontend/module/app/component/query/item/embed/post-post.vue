<template>

  <div @mouseleave="hover=false" @mouseover="hover=true"  class="query-item" v-bind="bind">

    <div class="i-content" :class="elmContent.class">

      <div class="i-media">

        <div class="c-logo">
          <img src="/statics/embed-logo.png"/>
        </div>

        <div class="i-thumb">
          <router-link :to="compUrl">
            <img v-lazy="itemImageThumbSrc"
                 alt=""
                 class="i-thumb-image"
                 height="562"
                 width="900"
            />
          </router-link>
        </div>

        <div class="i-info q-pb-md s-overlay">

          <div class="block q-pt-lg q-px-md">

            <h3
              class="i-title q-ma-none"
              :class="elmTitle.class"
              v-if="itemTitle"
            >
              {{itemTitle}}
            </h3>

          </div>

          <div class="q-px-md q-mt-xs" >

            <h4 class="i-subtitle q-ma-none q-mt-sm" :class="elmSubtitle.class" >
              {{item.subtitle}}
            </h4>

          </div>

        </div>

      </div>

    </div>

  </div>

</template>

<script>
  import CItem from '../_item-post'
  import CItemMeta from '../meta/meta'
  import CShare from '../meta/share-dropdown'

  export default {
    name: 'item-1',
    extends: CItem,
    components: {
      CItemMeta,
      CShare
    },
    data() {
      return {
        hover: false,
        elementDefaults: {
          title: {
            defclass: 's-font-4xl'
          },
          subtitle: {
            defclass: 's-font-sm'
          },
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
  }

  .i-media {
    position: relative;
  }


  .i-info {
    width: 100%;
    position: absolute;
    bottom: 0;
    box-sizing: border-box;
    text-shadow: 0 0 2px rgba(0, 0, 0, 0.5);
  }

  .i-thumb {
    line-height: 0;
  }

  .i-thumb-image {
    max-width: 100%;
    height: auto;
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

  .c-logo {
    position: absolute;
    top: 20px;
    left: 20px;

    img {
      max-width: 150px;
    }
  }

</style>
