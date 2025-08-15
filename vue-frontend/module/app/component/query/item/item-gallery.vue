<template>
  <div class="query-item" v-bind="bind">

    <slick-carousel
      :infinite="false"
      @beforeChange="imagesSlideChange"
      ref="gallery"
    >
      <div
        :key="galleryItem._id"
        class="c-images-item"
        v-for="galleryItem of item.gallery"
      >

        <div class="resp">
          <div :style="{paddingBottom: respImagePadding}" class="resp__holder"></div>
          <div class="c-images-item-image resp__bimg"
               v-lazy:background-image="galleryItem.thumbs[screen.gt.md ? 'd1.9' : 'd1.25'].src"/>
        </div>

        <div class="image__meta s-font-md s-lh-normal q-gutter-y-xs q-px-md q-py-sm q-mt-xs"
             v-if="galleryItem.caption || galleryItem.author">
          <div class="image__meta_caption" v-if="galleryItem.caption">{{galleryItem.caption}}</div>
          <div class="image__meta_author" v-if="galleryItem.author">Автор: {{galleryItem.author}}</div>
        </div>

      </div>

    </slick-carousel>

    <q-tabs
      align="left"
      class=""
      inline-label
      v-model="slideImage"
    >
      <q-tab
        :key="index"
        :name="index"
        class="q-pa-none q-mr-xs"
        content-class="q-pt-none"
        v-for="(galleryItem, index) of item.gallery"
      >
        <div class="c-thumbs-item-image">

          <div class="resp">
            <div :style="{paddingBottom: respThumbPadding}" class="resp__holder"></div>
            <div class="resp__bimg" v-lazy:background-image="galleryItem.thumbs['m1.9'].src"/>
          </div>

        </div>

      </q-tab>

    </q-tabs>

  </div>
</template>

<script>
  import CItem from './_item-post'

  export default {
    name: 'item-gallery',
    props: {
      imageSizeSlideMobile: {default: 'd1.25'},
      imageSizeSlideDesktop: {default: 'd1.9'},
      imageSizeThumb: {default: 'm1.9'},
    },
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
        return this.getImageSizeResponsivePadding(this.screen.gt.md ? this.imageSizeSlideDesktop : this.imageSizeSlideMobile);
      },
      respThumbPadding() {
        return this.getImageSizeResponsivePadding(this.imageSizeThumb);
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
