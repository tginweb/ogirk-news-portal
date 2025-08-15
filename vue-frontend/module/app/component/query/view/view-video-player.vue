<template>

  <el-widget
    :bodyOnly="widget.bodyOnly"
    :moreLabel="widget.moreLabel"
    :moreUrl="widget.moreUrl"
    :skeleton="widget.skeleton"
    :title="widget.title"
  >

    <div ref="content">

      <q-resize-observer @resize="onResizableUpdate"/>

      <template v-if="isDesktop">

        <div class="row q-col-gutter-md">

          <div class="col-24 col-md-15">

            <query-item-video-player
              :autoplay="autoplay"
              :elements="screen.gt.sm ? {
                  excerpt: {limit: 270},
                  meta: true
                } : {
                  meta: true
                }"
              :item="currentItem"
              @playerEnded="playerEnded"
              v-if="currentItem"
            />

          </div>

          <div class="col-24 col-md-9">

            <template v-if="items && items.length">

              <q-scroll-area
                class="v-playlist full-width"
                dark
                ref="scrollTarget"
                style="height: calc(100% - 50px);"
                visible
              >

                <query-item-video-teaser
                  :class="{
                    'item-current': currentItemIndex === index
                  }"
                  :disableLink="true"
                  :elements="{
                    image: {
                      size: 'm1.78'
                    }
                  }"
                  :item="item"
                  :key="item._id"
                  :name="item._id"
                  @click.native="playItem(item)"
                  class="v-playlist__item q-mb-lg"
                  v-for="(item, index) of items"
                />

              </q-scroll-area>

              <q-btn
                @click="onNavMore"
                class="full-width q-mt-md"
                color="white"
                focus-within
                icon="fas fa-chevron-down"
                label="еще"
                outline
              ></q-btn>

            </template>

          </div>

        </div>

      </template>

      <template v-else>

        <div :data-slide="currentItemIndex" class="slider-wrap">

          <slick-carousel
            :arrows="false"
            :centerMode="true"
            :dots="false"
            :infinite="false"
            :slidesToScroll="1"
            :slidesToShow="1"
            :variableWidth="true"
            @beforeChange="imagesSlideChange"
            centerPadding="0px"
            v-if="items && items.length"
          >
            <div
              :key="item._id"
              :name="item._id"
              :style="{width: contentWidth ? (contentWidth-50) + 'px' : '5vw'}"
              v-for="(item, index) of items"
            >
              <query-item-7
                :disableLink="true"
                :item="item"
                class="q-px-sm"
                imageSize="t1.5"
              />

            </div>

          </slick-carousel>

        </div>

      </template>

    </div>

  </el-widget>

</template>

<script>
  import CQueryView from '@common/query/component/view/view'

  const diff = require('deep-diff').diff;

  export default {
    extends: CQueryView,
    data() {
      return {
        contentWidth: null,
        autoplay: false
      }
    },
    methods: {
      onResizableUpdate(size) {
        this.contentWidth = size.width
      },
      imagesSlideChange(oldSlideIndex, newSlideIndex) {
        this.setCurrentItem(newSlideIndex, true)
      },
      playItem(item) {
        this.autoplay = true
        this.setCurrentItem(item, this)
      },
      playerEnded() {
        if (this.currentItemIndex < this.items.length - 1) {
          this.playItem(this.currentItemIndex + 1, this)
        }
        console.log('PLAY END')
      }
    },
    created() {
      if (this.itemsFirst)
        this.setCurrentItem(this.itemsFirst)
    },
    watch: {
      itemsFirst(item) {
        if (item)
          this.setCurrentItem(item)
      },
    }
  }
</script>

<style lang="scss" scoped>

  .slider-wrap[data-slide="0"] {
    /deep/ .slick-track {
      transform: translate3d(-8px, 0px, 0px) !important;
    }
  }

  .v-playlist__item {
    white-space: normal;
    text-transform: none;
    text-align: left;
    cursor: pointer;

    &.item-current {
      background-color: #333;
    }
  }

  .h-playlist__item {

  }

  .h-playlist__item__image {
    width: 140px;
    height: auto;
    max-width: none !important;
  }

</style>
