<template>

  <div class="com">

    <div v-html="data.text"></div>

  </div>

</template>

<script>

  export default {
    props: {
      data: {}
    },
    data() {
      return {}
    },
  }

</script>

<style lang="scss" scoped>

  /deep/ {
    p:last-child {
      margin-bottom: 0;
    }
  }

</style>
