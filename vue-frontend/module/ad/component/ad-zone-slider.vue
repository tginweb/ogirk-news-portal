<template>

  <div
    :class="{
      'demo-selected': isDemoSelectedZone
    }"
    :id="'ad-zone-' + zone.slug"
    class="c-zone"
    v-if="isMounted && (items.length || $slots.empty)"
  >

    <slick-carousel
      :arrows="false"
      :adaptive-height="true"
      :dots="true"
      :autoplay-speed="4000"
      :autoplay="true"
      dots-class="slick-dots custom-dot-class"
      class="c-zone"
      v-if="items.length"
    >
      <component
        :key="item._id"
        v-for="item of items"
        :is="item.is"
        :item="item"
        class="c-slide"
      />
    </slick-carousel>
  </div>


</template>

<script>

import CParent from './ad-zone'

export default {
  extends: CParent,
  data() {
    return {
      slide: 0,
      hover: false,
      isMounted: false
    }
  },
  mounted() {
    setTimeout(()=>{
      this.isMounted = true
    }, 100)
  },
  methods: {
    onNext() {
      this.$refs.carousel.next()
    },
    onPrev() {
      this.$refs.carousel.previous()
    },
  }
}
</script>

<style lang="scss" scoped>

.c-slide {
  img {
    -webkit-touch-callout: none; /* iOS Safari */
    -webkit-user-select: none; /* Safari */
    -khtml-user-select: none; /* Konqueror HTML */
    -moz-user-select: none; /* Old versions of Firefox */
    -ms-user-select: none; /* Internet Explorer/Edge */
    user-select: none;
  }

}

.c-zone {


  &.demo-selected {

    /deep/ {
      .i-content {
        background-color: #ff2222 !important;
      }
    }

  }
}

</style>
