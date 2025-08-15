<template>
  <div class="query-item" v-bind="bind">

    <div class="i-content bg-grey-8 text-center text-white flex" :style="{height: bannerHeight}">

      <div
        class="i-title text-weight-bold q-mx-auto q-my-auto"
        :style="{
         fontSize: (bannerHeight > 160 ? 140 : (parseInt(bannerHeight)-20)) + 'px'
        }"
      >{{item.title}}</div>

    </div>

  </div>
</template>

<script>
  import CItem from '~module/app/component/query/item/_item-post'
  import MItemBannered from '~module/app/mixin/item-bannered'

  export default {
    name: 'item-ad-banner',
    extends: CItem,
    mixins: [MItemBannered],
    data() {
      return {}
    },
    methods: {},
    computed: {

      bannerHeight() {
        return (this.item.meta.banner_height || 200) + 'px'
      },

      compUrl() {
        return ''
      },

    }
  }
</script>

<style lang="scss" scoped>

  .i-content {
    height: 300px;
  }

  .i-title {
    line-height: 0;

  }

</style>
