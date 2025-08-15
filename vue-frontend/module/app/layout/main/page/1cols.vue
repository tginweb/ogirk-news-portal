<template>

  <div class="container">

    <div class="page__header q-mb-lg" v-if="$slots.header">

      <slot name="header"/>

    </div>

    <slot/>

  </div>

</template>

<script>
  export default {

  }
</script>

<style lang="scss">


</style>
