<template>
  <div
    v-if="isMounted"
    :class="{
      'com': true,
      '--dark': dark
    }"
  >

    <div class="images">

      <q-btn
        v-if="!fullscreen"
        class="images__zoom"
        color="white"
        dense
        flat
        @click.capture="$store.dispatch('dialogShow', ['app/gallery', {
              gallery: items,
              galleryIndex: slideImage
            }])"
      >
        <q-icon name="zoom_out_map" size="md"/>
      </q-btn>

      <slick-carousel
        ref="images"
        :arrows="false"
        :infinite="false"
        :speed="300"
        class="images__carousel"
        @afterChange="sliderAfterChange"
        @beforeChange="sliderBeforeChange"
      >
        <div
          v-for="item of items"
          :key="item._id"
          class="image"
        >

          <div class="resp">
            <div :style="{paddingBottom: respSlidePadding}" class="resp__holder"></div>
            <div
              v-bind="bindImageContent"
              v-lazy:background-image="item.thumbs[slideImageSize].src"
              :class="{
                  '-crop-disable': item.gallery_crop_disable
                }"
              class="image__content resp__bimg"
            />
            <div v-if="item.caption || item.author" class="resp__content">
              <div class="image__meta s-font-md s-lh-normal q-gutter-y-xs q-px-md q-py-sm q-mt-xs">

                <div v-if="item.caption" class="image__meta_caption">{{ item.caption }}</div>
                <div v-if="item.author" class="image__meta_author">Автор: {{ item.author }}</div>
              </div>
            </div>
          </div>

        </div>

      </slick-carousel>

    </div>

    <div v-if="items.length > 1" class="thumbs">

      <slick-carousel
        ref="thumbs"
        :arrows="true"
        :focusOnSelect="false"
        :infinite="false"
        :slidesToScroll="6"
        :slidesToShow="6"
        :speed="300"
        class="thumbs__carousel"
        @afterChange="thumbsAfterChange"
        @beforeChange="thumbsBeforeChange"

      >
        <div
          v-for="(item, index) of items"
          :key="index"
          :class="{
            'current': slideImage === index
          }"
          :name="index"
          class="thumbs_slide cursor-pointer"
        >
          <div
            class="thumbs_slide_inner q-mr-xs"
            @click="slideImage = index"
          >
            <div class="resp">
              <div :style="{paddingBottom: respThumbPadding}" class="resp__holder"></div>
              <div v-lazy:background-image="item.thumbs[imageSizeThumb].src" class="resp__bimg"/>
            </div>
          </div>
        </div>

      </slick-carousel>

    </div>

  </div>
</template>

<script>
export default {
  name: 'gallery',
  props: {
    imageSizeSlideMobile: {default: 'd1.25'},
    imageSizeSlideDesktop: {default: 'd1.9'},
    imageSizeThumb: {default: 'm1.9'},
    items: {},
    fullscreen: {default: false},
    imageBgColor: {default: '#ddd'},
    dark: {default: true}
  },
  data() {
    return {
      slideImage: 0,
      queue: [],
      sliding: false,
      isMounted: false
    }
  },
  methods: {
    sliderBeforeChange(oldSlideIndex, newSlideIndex) {
      this.slideImage = newSlideIndex
      this.sliding = true
    },
    sliderAfterChange(newSlideIndex) {
      this.slideImage = newSlideIndex
      this.sliding = false
      this.nextQueue()
    },
    thumbsAfterChange(newSlideIndex) {

    },
    thumbsBeforeChange(oldSlideIndex, newSlideIndex) {

    },
    getImageSizeResponsivePadding(size) {
      const sizeInfo = this.$config.data.IMAGE.SIZES[size]
      if (sizeInfo) {
        return (Math.round(Math.abs((sizeInfo.args[1] / sizeInfo.args[0]) * 100) * 100) / 100) + '%';
      } else {
        return '50%';
      }
    },
    nextQueue() {
      this.$nextTick(() => {
        if (!this.sliding && this.queue.length) {
          this.$refs.images.goTo(this.queue.shift())
        }
      })

    },

  },
  watch: {
    slideImage(val) {

      this.queue.push(val)
      this.nextQueue();
    },
  },
  computed: {

    bindImageContent() {
      const res = {
        style: {
          backgroundColor: this.items.length > 1 ? '#000' : '#eee'
        }
      }
      return res
    },

    slideImageSize() {
      return this.screen.gt.md ? this.imageSizeSlideDesktop : this.imageSizeSlideMobile
    },

    respSlidePadding() {
      return this.getImageSizeResponsivePadding(this.screen.gt.md ? this.imageSizeSlideDesktop : this.imageSizeSlideMobile);
    },
    respThumbPadding() {
      return this.getImageSizeResponsivePadding(this.imageSizeThumb);
    }
  },


  mounted() {

    this.$nextTick(() => {

      setTimeout(() => {
        this.isMounted = true
      }, 70)

    })

  }
}
</script>

<style lang="scss" scoped>

.com {
  &:not(.--dark) {
    /deep/ {
      .slick-arrow:before {
        color: #222;
      }
    }
  }

  &.--dark {
    /deep/ {
      .slick-arrow:before {
        color: #fff;
      }
    }
  }
}

.images {
  position: relative;
}

.images__zoom {
  position: absolute;
  top: 10px;
  right: 20px;
  z-index: 10;
}

.image {
  outline: none;

  .image__content {
    background-size: cover;

    &.-crop-disable {
      background-size: contain;
    }
  }

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


.thumbs__carousel {
  display: flex !important;

  /deep/ {
    .slick-list {
      width: 100%;
    }

    .slick-arrow {
      margin: 0 3px 0 3px;
      position: static;
      margin-top: auto;
      margin-bottom: auto;
      transform: none;
    }
  }

  .thumbs_slide {
    outline: none;
    padding: 2px;

    &.current {

      .thumbs_slide_inner {
        outline: 2px solid #fff;
      }
    }
  }

}

</style>
