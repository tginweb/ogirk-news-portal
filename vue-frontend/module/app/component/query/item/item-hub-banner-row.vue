<template>
  <div class="query-item" v-bind="bind">


    <component
      class="i-content"
      v-bind="_bindLink"
    >
      <template v-if="screen.gt.sm">

        <div :style="{backgroundColor: bannerBgColor}" class="i-mask"></div>

        <div class="i-info">
          <div>

            <div class="i-title" v-html="bannerTitle"></div>

            <div class="i-subtitle q-mt-md" v-html="bannerSubtitle"></div>

          </div>
        </div>

      </template>
      <template v-else>

        <div class="i-media">
          <div class="i-thumb">
            <img
              v-lazy="bannerBgImage"
              class="i-thumb-image"
            />
          </div>
        </div>
        <div class="i-info">
          <h3 class="i-title q-ma-none">
            {{bannerTitle}}
          </h3>
        </div>

      </template>


    </component>

  </div>
</template>

<script>
  import CItem from './_item-post'
  import MItemBannered from '~module/app/mixin/item-bannered'

  export default {
    name: 'item-hub-banner-row',
    components: {},
    extends: CItem,
    mixins: [MItemBannered],
    data() {
      return {
        imageWidth: 600
      }
    },
    methods: {},
    computed: {

      _bindLink() {
        const res = {
          ...this.bannerBind,
          ...this.bindLink
        }
        delete res.style['background-color']
        return res
      },

      compUrl() {
        return this.item.meta.sm_hub_landing_url || this.routeHost.url || ''
      },
    }
  }
</script>

<style lang="scss" scoped>

  .query-item {
    height: 290px;
  }

  .i-content {

    display: block;
    background-repeat: no-repeat;
    height: 100%;
    width: 100%;
    position: relative;
    overflow: hidden;

    .i-mask {
      -webkit-transform: rotate(65deg) scale(3) translateY(51%);
      -ms-transform: rotate(65deg) scale(3) translateY(51%);
      transform: rotate(65deg) scale(3) translateY(51%);
      width: 100%;
      height: 100%;
      position: absolute;
      left: 20px;
    }

    .i-info {
      position: relative;
      width: 50%;
      height: 100%;
      padding: 62px 31px 26px 20px;
      overflow: hidden;
      color: #FFF;
    }

    .i-title {
      font-size: 28px;
      line-height: 32px;
      font-weight: 800;
    }

    .i-subtitle {
      font-size: 20px;
      line-height: 26px;
      margin-top: 15px;
      font-weight: 500;
    }


  }


</style>
