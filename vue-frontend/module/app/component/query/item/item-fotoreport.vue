<template>
  <div class="query-item" v-bind="bind">

    <el-gallery :items="item.gallery"/>

    <h3 class="i-title q-ma-none q-mt-md">
      <router-link :to="compUrl">
        {{itemTitle}}
      </router-link>
    </h3>

    <div class="i-excerpt q-mt-sm" v-if="itemExcerpt">

      {{itemExcerptFormatted}}

      <router-link :to="compUrl">
        <q-icon name="fas fa-caret-right"/>
        <u>читать далее</u>
      </router-link>

    </div>

    <div class="i-meta flex items-center q-mt-md">

      <div class="i-date">
        {{$util.date.timestampToFormat(item.created, 'datetime')}}
      </div>

      <q-space></q-space>

      <div class="i-social q-gutter-x-sm">
        <el-share
          :sharing="{
            url: this.item.url,
            title: this.itemTitle,
            description: this.item.description
          }"
          class="q-gutter-sm"
        />
      </div>

    </div>


  </div>
</template>

<script>
  import CItem from './_item-post'

  export default {
    name: 'item-gallery',
    extends: CItem,
    data() {
      return {
        slideImage: 0,
      }
    },
    methods: {
      imagesSlideChange(oldSlideIndex, newSlideIndex) {
        this.slideImage = newSlideIndex
      }
    },
    watch: {
      slideImage(val) {
        this.$refs.gallery.goTo(val)
      }
    },
    computed: {
      respImagePadding() {
        return this.getImageSizeResponsivePadding(this.screen.gt.md ? 'd1.9' : 'd1.25');
      },
      respThumbPadding() {
        return this.getImageSizeResponsivePadding('m1.9');
      }
    },
  }
</script>

<style lang="scss" scoped>

  .i-title {
    font-size: 20px;
    font-weight: 700;
    line-height: 1.5em;
  }

  .c-images-item-image {
    max-width: 100%;
    height: auto;
    pointer-events: none;
  }

  .c-thumbs-item-image {
    width: 140px;
    height: auto;
  }

</style>
