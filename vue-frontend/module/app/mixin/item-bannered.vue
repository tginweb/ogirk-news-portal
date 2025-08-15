<template>
  <div class="query-item">

  </div>
</template>

<script>

  export default {
    components: {},
    data() {
      return {}
    },
    methods: {},
    computed: {

      bannerTitle() {
        return this.item.meta.banner_title || this.itemTitle
      },

      bannerSubtitle() {
        return this.item.meta.banner_subtitle || this.item.subtitle
      },

      bannerBgColor() {
        return this.item.meta.banner_bg_color || '#255a75'
      },

      bannerBgImage() {
        const url = this.item.meta.banner_bg_image || this.itemImageSrc
        return this.$image.resolveUrl(url, this.imageWidth)
      },

      bannerBgPositionX() {
        if (this.item.meta.banner_bg_position_x == 'value') return this.item.meta.banner_bg_position_x_value
        return this.item.meta.banner_bg_position_x || 'right'
      },

      bannerBgPositionY() {
        if (this.item.meta.banner_bg_position_y == 'value') return this.item.meta.banner_bg_position_y_value
        return this.item.meta.banner_bg_position_y || 'center'
      },

      bannerBgSizeX() {
        return this.item.meta.banner_bg_size_x || 'auto'
      },

      bannerBgSizeY() {
        return this.item.meta.banner_bg_size_y || '100%'
      },

      bannerBind() {

        let res = {
          style: {},
          class: {}
        }

        res.style['background-color'] = this.bannerBgColor;
        res.style['background-image'] = 'url(' + this.bannerBgImage + ')';
        res.style['background-position'] = this.bannerBgPositionX + ' ' + this.bannerBgPositionY;
        res.style['background-size'] = this.bannerBgSizeX + ' ' + this.bannerBgSizeY;

        return res
      }
    }
  }
</script>

<style lang="scss" scoped>


</style>
