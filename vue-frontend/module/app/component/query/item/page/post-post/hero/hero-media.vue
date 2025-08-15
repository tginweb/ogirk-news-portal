<template>
  <div class="com">

    <el-gallery
      :items="item.gallery && item.gallery.length ? item.gallery : [itemImage]"
      v-if="itemImage || item.gallery && item.gallery.length "
      :dark="false"
    />

  </div>
</template>

<script>
  import CItem from './../../../_item-post'

  export default {
    extends: CItem,
    components: {},
    data() {
      return {
        slideImage: 0,
      }
    },
    mounted() {
      this.$nextTick(() => {

      })
    },
    computed: {},
    methods: {
      onSliderGalleryChange(oldSlideIndex, newSlideIndex) {
        this.slideImage = newSlideIndex
      },
      onSliderThumbsChange(oldSlideIndex, newSlideIndex) {
        this.slideImage = newSlideIndex
      }
    },
    watch: {
      slideImage(val) {
        this.$refs.gallery.goTo(val)
      }
    }
  }
</script>

<style lang="scss" scoped>

  .com {

  }

  .gallery {
    overflow: hidden;
    position: relative;
  }

  .gallery__carousel {
    height: auto;
  }

  .gallery__slide {
    line-height: 0;
    height: auto;
    position: relative;
  }

  .gallery__slide__image {
    max-width: 100%;
    height: auto;
    pointer-events: none;
  }

  .gallery__slide__zoom {
    position: absolute;
    top: 10px;
    right: 20px;
    z-index: 10;
  }

  .thumbs__slide {

  }

  .thumbs__slide__image {
    width: 140px;
    height: auto;
  }

  .image {
    position: relative;
    line-height: 0;
    font-size: 0;

    .image__meta {
      position: absolute;
      bottom: 0;
      color: #FFF;
      width: 100%;
      background-color: rgba(0, 0, 0, 0.3);
    }

    .image__meta_caption {
      font-size: 90%;
    }

    .image__meta_author {
      font-size: 80%;
      color1: #888;
    }
  }

  .single {
    overflow: hidden;
  }

</style>
