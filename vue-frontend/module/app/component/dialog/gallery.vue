<template>

  <q-dialog
    :maximized="true"
    content-class="dialog-gallery flex"
    v-model="visible"
  >

    <q-resize-observer @resize="onResizeBody"/>

    <div
      :class="{
        'dialog q-my-auto': true,
        '-is-mobile': isMobile,
        ['-orientation-' + screenOrientation]: true
      }"
      :style="{width: dialogWidth}"
      style="background: transparent;box-shadow:none; overflow: hidden; height: auto !important;"
    >

      <a
        :href="slideImageData.src"
        target="_blank"
        v-if="!isMobile && slideImageData"
      >
        <q-btn
          class="download"
          color="white"
          dense
          flat
          :icon="$icons.fasDownload"
          round
          size="14px"
        />
      </a>


      <q-btn
        @click="onClose"
        class="closer"
        color="white"
        dense
        flat
        icon="close"
        round
        size="20px"
      />

      <div class="dialog__body">

        <div class="images">

          <slick-carousel
            :arrows="false"
            :infinite="false"
            :speed="300"
            @afterChange="sliderAfterChange"
            @beforeChange="sliderBeforeChange"
            class="images__carousel"
            ref="images"
            :initialSlide="galleryIndex"
          >
            <div
              :key="item._id"
              class="image"
              v-for="item of gallery"
            >

              <div class="resp">
                <div :style="{paddingBottom: respSlidePadding}" class="resp__holder"></div>
                <div
                  class="image__content resp__bimg"
                  :class="{
                    '-crop-disable': item.gallery_crop_disable
                  }"
                  v-bind="bindImageContent"
                  v-lazy:background-image="item.thumbs[slideImageSize].src"
                />
                <div class="resp__content" v-if="item.caption || item.author">
                  <div class="image__meta s-font-md s-lh-normal q-gutter-y-xs q-px-md q-py-sm q-mt-xs">

                    <div class="image__meta_caption" v-if="item.caption">{{item.caption}}</div>
                    <div class="image__meta_author" v-if="item.author">Автор: {{item.author}}</div>
                  </div>
                </div>
              </div>

            </div>

          </slick-carousel>

        </div>

        <div class="thumbs" v-if="showThumbs">

          <slick-carousel
            :arrows="true"
            :focusOnSelect="false"
            :infinite="false"
            :slidesToScroll="4"
            :slidesToShow="1"
            :variableWidth="true"
            :speed="300"
            @afterChange="thumbsAfterChange"
            @beforeChange="thumbsBeforeChange"
            class="thumbs__carousel"
            ref="thumbs"
            :initialSlide="galleryIndex"
          >
            <div
              :class="{
                'current': slideImage === index
              }"
              :key="index"
              :name="index"
              class="thumbs_slide"
              v-for="(item, index) of gallery"
              style="width:100px;"
            >
              <div
                @click="slideImage = index"
                class="thumbs_slide_inner q-mr-xs"
              >
                <div class="resp">
                  <div :style="{paddingBottom: respThumbPadding}" class="resp__holder"></div>
                  <div class="resp__bimg" v-lazy:background-image="item.thumbs[imageSizeThumb].src"/>
                </div>
              </div>
            </div>

          </slick-carousel>

        </div>

      </div>

    </div>

  </q-dialog>

</template>

<script>
  import CDialog from '@common/dialog/component/dialog'

  export default {
    extends: CDialog,
    props: {
      gallery: {},
      galleryIndex: {default: 0}
    },
    components: {},
    data() {
      return {
        dialogModule: 'app',
        dialogName: 'gallery',
        dialogWidth: '100%',
        imageSizePortrait: 'd1.25',
        imageSizeLandscape: 'd1.9',
        imageSizeThumb: 'm1.9',
        imageBgColor: '#000',
        slideImage: this.galleryIndex,
        queue: [],
        sliding: false,
        screenOrientation: 'landscape'
      }
    },
    computed: {
      slideImageData() {
        return this.slideImage >= 0 && this.gallery[this.slideImage]
      },

      showThumbs() {
        return (!this.isMobile || this.screenOrientation=='portrait') && (this.gallery.length > 1)
      },
      isMobile() {
        return this.screen.lt.md
      },

      bindImageContent() {
        const res = {
          style: {
            backgroundColor: this.imageBgColor
          }
        }
        return res
      },

      slideImageSize() {
        return this.screenOrientation == 'landscape' ? this.imageSizeLandscape : this.imageSizePortrait
      },

      respSlidePadding() {
        return this.getImageSizeResponsivePadding(this.slideImageSize);
      },
      respThumbPadding() {
        return this.getImageSizeResponsivePadding(this.imageSizeThumb);
      }
    },
    methods: {

      onClose() {

        console.log('fff')
       this.visible = false
      },

      onResizeBody() {
        this.screenOrientation = window.innerHeight > window.innerWidth ? 'portrait' : 'landscape'
        this.setDialogWidth()
      },

      setDialogWidth() {

        if (this.$q.screen.gt.lg) {

          let winHeight = window.innerHeight - 100

          if (this.showThumbs) {
            winHeight = winHeight - 100
          }

          const sizeInfo = this.$config.data.IMAGE.SIZES[this.slideImageSize]

          if (sizeInfo) {
            this.dialogWidth = Math.round(winHeight * (sizeInfo.args[0] / sizeInfo.args[1])) + 'px';
          }

        } else {
          this.dialogWidth = '100%';
        }
      },

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
    created() {
      this.setDialogWidth()
    },
    mounted() {

    },
    watch: {
      slideImage(val) {

        this.queue.push(val)
        this.nextQueue();
      },
    }
  }

</script>

<style lang="scss">

  .dialog-gallery {

    .q-dialog__inner {
      padding: 0 !important;
      max-height: none;
    }

    .dialog {
      &.-is-mobile.-orientation-landscape {
        .image {
          .resp {
            height: 100vh;
          }
        }
      }
    }

    .dialog__body {
      position: relative;
    }

    .closer {
      position: fixed;
      right: 10px;
      top: 10px;
      z-index: 100;
      border-radius: 0;
      background: rgba(0,0,0,0.5);
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
  }


  .download {
    position: fixed;
    left: 10px;
    top: 10px;
    z-index: 100;
    border-radius: 0;
    background: rgba(0,0,0,0.5);
  }


</style>



