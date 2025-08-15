<template>

  <div
    @mouseleave="hover=false"
    @mouseover="hover=true"
    class="query-item"
    v-bind="bind"
  >


    <div class="i-content">

      <div
        v-lazy:background-image="bannerBgImage"
        class="i-media"
      >
        <q-btn
          :label="formatInfo.label"
          class="i-format style-1"
          dense
          size="md"
          v-if="formatInfo"
        >
          <q-icon :name="formatInfo.icon" class="q-ml-sm" size="12px"/>
        </q-btn>

        <div class="i-terms -style1 q-ma-md q-px-xs" v-if="termsCategory.length>0">
          <span :key="term._id" class="i-terms-item" v-for="term of termsCategory.slice(0,1)">
            {{term.name}}
          </span>
        </div>

        <div
          :style="{
            backgroundImage: `
              linear-gradient(-180deg,
              rgba(${bgColorRGB.r}, ${bgColorRGB.g}, ${bgColorRGB.b}, 0) 7%,
              rgba(${bgColorRGB.r}, ${bgColorRGB.g}, ${bgColorRGB.b}, 0.08) 12%,
              rgba(${bgColorRGB.r}, ${bgColorRGB.g}, ${bgColorRGB.b}, 0.16) 17%,
              rgba(${bgColorRGB.r}, ${bgColorRGB.g}, ${bgColorRGB.b}, 0.22) 21%,
              rgba(${bgColorRGB.r}, ${bgColorRGB.g}, ${bgColorRGB.b}, 0.32) 26%,
              rgba(${bgColorRGB.r}, ${bgColorRGB.g}, ${bgColorRGB.b}, 0.42) 32%,
              rgba(${bgColorRGB.r}, ${bgColorRGB.g}, ${bgColorRGB.b}, 0.52) 38%,
              rgba(${bgColorRGB.r}, ${bgColorRGB.g}, ${bgColorRGB.b}, 0.62) 47%,
              rgba(${bgColorRGB.r}, ${bgColorRGB.g}, ${bgColorRGB.b}, 0.72) 57%,
              rgba(${bgColorRGB.r}, ${bgColorRGB.g}, ${bgColorRGB.b}, 0.82) 65%)
            `
          }"
          class="i-info q-pb-md"
        >

          <component class="block q-pt-xl q-px-md" v-bind="bindLink">

            <h3 class="i-title q-ma-none">{{bannerTitle}}</h3>

          </component>

          <transition name="slide-fade" v-if="bannerSubtitle">

            <div class="q-mt-xs" v-if="hover">

              <component class="block q-px-md" v-bind="bindLink" v-if="bannerSubtitle">

                <h4 class="i-subtitle q-ma-none">
                  {{bannerSubtitle}}
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
  import MItemBannered from '~module/app/mixin/item-bannered'

  export default {
    name: 'item-hub-banner-col',
    extends: CItem,
    mixins: [MItemBannered],
    components: {
      CItemMeta
    },
    data() {
      return {
        hover: false,
        imageWidth: 600
      }
    },
    methods: {},
    computed: {

      bgColorRGB() {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(this.item.meta.banner_bg_color || '#255a75');
        return result ? {
          r: parseInt(result[1], 16),
          g: parseInt(result[2], 16),
          b: parseInt(result[3], 16)
        } : null;
      },

      compUrl() {
        return this.item.meta.sm_hub_landing_url || this.routeHost.url || ''
      },
    }
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
    height: 180px;
    background-size: cover;
  }

  .i-info {
    width: 100%;
    position: absolute;
    bottom: 0;
    box-sizing: border-box;
    text-shadow: 0 0 2px rgba(0, 0, 0, 0.5);
  }

  .i-title {
    font-size: 20px;
    line-height: 1.3em;
    font-weight: 700;

    a {
      display: block;;
    }
  }

  .i-subtitle {
    font-size: 16px;
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
