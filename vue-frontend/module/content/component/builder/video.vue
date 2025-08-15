<template>

  <div class="com">

    <el-player type="video" :source="{url: data.video}"/>

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


</style>
