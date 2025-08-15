<template>

  <div v-if="isMounted">
    <slick-carousel
      v-bind="bind"
    >
      <div
        :key="item._id"
        v-for="item of items"
      >
        <component
          v-bind="bindItem(item, item._id)"
        />
      </div>
    </slick-carousel>
  </div>

</template>

<script>
  import Items from '@common/query/component/items/items'

  export default {
    extends: Items,
    props: {
      slider: {
        default: () => ({})
      },
      paginationStyle: {
        default: () => ({})
      }
    },
    data() {
      return {
        slide: 0,
        isMounted: false
      }
    },
    mounted() {
      setTimeout(()=>{
        this.isMounted = true
      }, 100)
    },
    computed: {
      bind() {
        return {
          arrows: false,
          adaptiveHeight: false,
          dots: true,
          dotsClass: "slick-dots custom-dot-class",
          ...this.slider,
        }
      },
    }
  }
</script>

<style lang="scss" scoped>

  .com {
    padding-bottom: 35px;

    /deep/ {
      .q-carousel__navigation--bottom {
        bottom: -5px;
      }
    }
  }

</style>
