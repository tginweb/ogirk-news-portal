<template>

  <div class="container">

    <div class="page__top q-mb-xl" v-if="$slots.top">

      <slot name="top"/>

    </div>

    <div class="page__row row q-col-gutter-xl">

      <div class="page__main col-24 col-md-16">

        <div class="page__header q-mb-lg" v-if="$slots.header">

          <slot name="header"/>

        </div>

        <slot/>

      </div>

      <div class="page__right col-24 col-md-8">

        <slot name="right"/>

      </div>

    </div>

  </div>

</template>

<script>
  export default {

  }
</script>

<style lang="scss">


</style>
