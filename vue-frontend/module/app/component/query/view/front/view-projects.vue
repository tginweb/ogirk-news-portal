<template>

  <el-widget
    :bodyOnly="widget.bodyOnly"
    :haveNext="true"
    :havePrev="slide > 0"
    :loading="loading"
    :moreSide="false"
    :moreUrl="widget.moreUrl"
    :skeleton="widget.skeleton"
    :title="widget.title"
    @next="onNext"
    @prev="onPrev"
  >
    <template v-slot:default>
      <q-carousel
        :autoplay="hover ? false : 4000"
        infinite
        @mouseout="hover=false"
        @mouseover="hover=true"
        animated
        class="c-carousel"
        control-color="secondary"

        ref="carousel"
        swipeable
        transition-next="slide-left"
        transition-prev="slide-right"
        v-model="slide"
      >
        <q-carousel-slide
          :key="groupIndex"
          :name="groupIndex"
          class="c-slide column no-wrap"

          v-for="(group, groupIndex) of itemsGrouped"
        >

          <div class="row justify-start items-center q-col-gutter-lg ">
            <component :is="screen.gt.sm ? 'query-item-hub-banner-row' : 'query-item-hub-banner-col'"
              :item="item"
              :key="itemIndex"
              class="col-24 col-sm-12"
              v-for="(item, itemIndex) of group"
            >
            </component>
          </div>

        </q-carousel-slide>
      </q-carousel>
    </template>

  </el-widget>

</template>

<script>
  import CQueryView from '@common/query/component/view/view'

  export default {
    extends: CQueryView,
    data() {
      return {
        slide: 0,
        hover: false
      }
    },
    computed: {
      itemsGrouped() {

        let res = [], groupIndex = 0, index = 0;

        if (!this.loaded || !this.items) return res;

        this.items.forEach((item) => {

          if (index % 2 == 0) {
            res.push([]);
            groupIndex = res.length - 1;
          }

          index++;

          res[groupIndex].push(item)
        })

        return res;
      }
    },
    methods: {
      onNext() {
        this.$refs.carousel.next()
      },
      onPrev() {
        this.$refs.carousel.previous()
      },
    },
    created() {

    }
  }
</script>

<style lang="scss" scoped>


  .c-carousel {
    height: auto;
    padding-bottom1: 62px;
  }

  .c-slide {
    padding: 0;
  }

</style>
