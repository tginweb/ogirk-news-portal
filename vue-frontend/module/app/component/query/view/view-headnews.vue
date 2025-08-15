<template>

  <div :class="{['status-'+currentItemStatus]: true}" class="com">

    <div class="row items-center">

      <div
        class="header  col-24 col-md-auto col-shrink gt-md  q-pr-sm q-py-xs bg-grey1-3 text-grey-9 text-bold s-font-xxs s-font-md-sm">
        НОВОСТИ:
      </div>

      <div class="content col-24 col-md-auto ">

        <q-carousel
          @mouseout="startAutoplay"
          @mouseover="stopAutoplay"
          animated
          class="slider"
          control-color="primary"
          infinite
          ref="carousel"
          swipeable
          transition-next="slide-left"
          transition-prev="slide-right"
          v-model="slide"
        >
          <q-carousel-slide
            :key="index"
            :name="index"
            class="flex no-wrap items-center"
            style="padding: 0;"
            v-for="(item, index) in items"
          >
            <q-icon
              @click="$refs.carousel.previous()"
              name="arrow_left"
              size="sm"
            />

            <h5 class="i-title s-font-xs s-font-md-sm q-ma-none">

              <router-link :to="item.url">

                {{item.title}}

              </router-link>

            </h5>

            <q-icon
              @click="$refs.carousel.next()"
              name="arrow_right"
              size="sm"
            />

          </q-carousel-slide>

          <template v-slot:control>

          </template>

        </q-carousel>

      </div>

    </div>

  </div>

</template>

<script>
  import Items from '@common/query/component/items/items'

  export default {
    extends: Items,
    data() {
      return {
        slide: 0,
        timer: 0,
        timerInterval: null
      }
    },
    computed: {
      currentItem() {
        return this.items[this.slide]
      },
      currentItemStatus() {
        if (this.currentItem) {
          if (this.currentItem.taxonomy['sm-role'].indexOf(7843) > -1) return 'error'
        }
        return 'info'
      },
    },
    mounted() {
      this.startAutoplay();
    },
    beforeDestroy() {
      this.stopAutoplay();
    },
    methods: {

      startAutoplay() {
        this.timerInterval = setTimeout(() => {
          this.goSlideNext();
          this.startAutoplay();
        }, this.currentItemStatus === 'error' ? 20000 : 5000)
      },

      stopAutoplay() {
        if (this.timerInterval)
          clearInterval(this.timerInterval)
      },

      goSlideNext() {
        this.$refs.carousel && this.$refs.carousel.next()
      }
    }
  }
</script>

<style lang="scss" scoped>

  .com {
    .q-carousel__control {
      color: #222;
    }

    &.status-error {
      .i-title {
        a {
          background-color: #ca3538;
          color: #FFF;
        }
      }
    }

    &.status-info {
      .i-title {
        a {
          color: #111;
        }
      }
    }
  }

  .slider {
    height: auto;
    background-color: transparent;

    /deep/ {
      .q-carousel__navigation--bottom {
        bottom: -5px;
      }
    }
  }

  .i-title {
    font-weight: 400;
    line-height: 1.3em;

    a {
      display: inline-block;
      padding: 3px 10px 3px 10px;
      text-decoration: none;
      background-color: #eee;
    }
  }


</style>
