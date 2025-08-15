<template>

  <q-carousel
    class="com"
    control-color="primary"
    navigation
    swipeable
    v-model="slide"
    style="height: auto;"
  >
    <q-carousel-slide
      :key="index"
      :name="index"
      v-for="(item, index) in items"
      style="padding: 0;"
    >
      <component
        v-bind="bindItem(item, index)"
      >
      </component>
    </q-carousel-slide>
  </q-carousel>

</template>

<script>
  import Items from '@common/query/component/items/items'

  export default {
    extends: Items,
    data() {
      return {
        slide: 0
      }
    },
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
