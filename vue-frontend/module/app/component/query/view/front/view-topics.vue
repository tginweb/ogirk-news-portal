<template>
  <div>

    <el-skeleton v-if="loading"/>

    <div v-else-if="loaded">


      <div class="container q-mb-md">

        <div class="flex  items-center" style="overflow: auto;">

          <q-tabs
            active-color="white"
            active-bg-color="grey-6"
            class=""
            dense
            flat
            indicator-color="transparent"
            inline-label
            align="left"
            v-model="slide"
          >
            <template
              v-for="(item, index) of items"
            >
              <q-tab
                :key="item._id"
                :label="item.title"
                :name="item._id"
              />
            </template>
          </q-tabs>

          <div class="q-ml-md" v-if="false">
            все сюжеты
          </div>

        </div>

      </div>


      <q-carousel
        animated
        class="c-carousel"
        control-color="primary"
        swipeable
        transition-next="slide-left"
        transition-prev="slide-right"
        v-model="slide"
        :autoplay="5000"
        infinite
      >
        <q-carousel-slide
          :key="item._id"
          :name="item._id"
          class="c-slide column no-wrap"
          v-for="item of items"
        >

          <query-item-topic
            :item="item"
            :elements="{
              title: {
                class: ''
              }
            }"
          >
          </query-item-topic>

        </q-carousel-slide>

      </q-carousel>

    </div>

  </div>
</template>

<script>
  import CQueryView from '@common/query/component/view/view'

  export default {
    extends: CQueryView,
    data() {
      return {
        slide: null,
      }
    },
    watch: {
      itemsFirst: {
        immediate: true,
        handler(item) {
          this.slide = item && item._id
        },
      },
    }
  }
</script>

<style lang="scss" scoped>

  .c-carousel {
    height: auto;
  }

  .c-slide {
    padding: 0;
  }

</style>
