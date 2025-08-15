<template>
  <div class="query-item" v-bind="bind">

    <component
      class="i-content"
      v-bind="_bindLink"
      target="_blank"
    >

      <div class="i-info">
        <div class="i-title" v-html="markupProcess(item.meta.banner_title)" v-bind="bindTitle">
        </div>
        <div class="i-subtitle q-mt-md" v-html="markupProcess(item.meta.banner_subtitle)" v-bind="bindSubtitle">
        </div>
      </div>

    </component>

  </div>
</template>

<script>
  import CItem from '~module/app/component/query/item/_item-post'
  import MItemBannered from '~module/app/mixin/item-bannered'

  export default {
    name: 'item-ad-constructor',
    extends: CItem,
    mixins: [MItemBannered],
    data() {
      return {
        imageWidth: 400
      }
    },
    methods: {},
    computed: {

      bindTitle() {
        const res = {
          style: {}
        }
        return res
      },

      bindSubtitle() {
        const res = {
          style: {}
        }
        return res
      },

      compUrl() {
        let url = this.item.meta.sm_ad_link || ''

        if (this.item.meta.er_token) {
          url = url.indexOf('?') !== -1 ? url + '&erid=' + this.item.meta.er_token : url + '?erid=' + this.item.meta.er_token
        }

        return url
      },

      _bindLink() {
        return {
          ...this.bannerBind,
          ...this.bindLink
        }
      },

    }
  }
</script>

<style lang="scss" scoped>

  .query-item {
    height: 150px;
  }

  .i-content {
    display: block;
    background-repeat: no-repeat;
    height: 100%;
  }

  .i-media {

  }

  .i-info {
    position: relative;
    width: 100%;
    padding: 20px 0 0 24px;
    overflow: hidden;
  }

  .i-title {
    font-size: 19px;
    line-height: 25px;
    font-weight: bold;
  }

  .i-subtitle {
    font-size: 17px;
    line-height: 20px;
    opacity: .6;
  }

</style>
