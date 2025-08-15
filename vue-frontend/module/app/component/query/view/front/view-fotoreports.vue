<template>

  <el-widget
    :bodyOnly="widget.bodyOnly"
    :loading="loading"
    :moreLabel="widget.moreLabel"
    :moreUrl="widget.moreUrl"
    :skeleton="widget.skeleton"
    :title="widget.title"
  >
    <template v-slot:default>

      <template v-if="screen.gt.sm">

        <div class="row q-col-gutter-lg">

          <div class="col-md-16">

            <q-tab-panels
              animated
              class="bg-transparent"
              dark
              swipeable
              transition-next="jump-up"
              transition-prev="jump-up"
              v-model="tab"
              vertical
            >
              <q-tab-panel
                :key="item._id"
                :name="item._id"
                class="q-pa-none "
                v-for="item of items"
              >
                <query-item-fotoreport
                  :item="item"
                />
              </q-tab-panel>

            </q-tab-panels>

          </div>

          <div class="col-md-8">

            <q-tabs
              class="text-teal"
              v-if="isMounted"
              v-model="tab"
              vertical
            >
              <q-tab
                :key="item._id"
                :name="item._id"
                class="c-tab q-mb-lg"
                content-class="full-width"
                v-for="item of items"
              >
                <query-item-3
                  :disableLink="true"
                  :item="item"
                  class="full-width q-pr-sm"
                  imageSize="t1.5"
                />
              </q-tab>

            </q-tabs>

          </div>

        </div>

      </template>

      <template v-else>

        <div class="slider-wrap" :data-slide="tabIndex">

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
              style="width: 80vw;"
              v-for="(item, index) of items"
            >
              <query-item-7
                :item="item"
                class="q-px-sm"
                imageSize="t1.5"
              />
            </div>

          </slick-carousel>

        </div>

      </template>

    </template>
  </el-widget>

</template>

<script>
  import CQueryView from '@common/query/component/view/view'

  export default {
    extends: CQueryView,
    data() {
      return {
        tab: null,
        isMounted: false
      }
    },
    methods: {
      setTab(val) {

        let id, item

        if (typeof val === 'object')
          id = val._id
        else if (typeof val === 'string')
          id = val
        else if (typeof val === 'number')
          id = this.items[val] && this.items[val]._id

        this.tab = id
      },

      imagesSlideChange(oldSlideIndex, newSlideIndex) {
        this.setTab(newSlideIndex)
      }
    },
    computed: {
      tabItem() {
        return this.tab && this.items.find(item => item._id === this.tab)
      },
      tabIndex() {
        return this.items.indexOf(this.tabItem)
      },
    },
    created() {
      this.setTab(this.itemsFirst)
    },
    mounted() {

      this.$nextTick(() => {
        setTimeout(() => {
          this.isMounted = true
        })
      }, 100)
    },
    watch: {
      itemsFirst(item) {
        if (item)
          this.setTab(item)
      },
    }
  }
</script>

<style lang="scss" scoped>

  .c-tab {
    white-space: normal;
    text-transform: none;
    text-align: left;
    padding-left: 0;
    padding-right: 0;
  }

  .slider-wrap[data-slide="0"] {
    /deep/ .slick-track {
      transform: translate3d(-8px, 0px, 0px) !important;
    }
  }

</style>
