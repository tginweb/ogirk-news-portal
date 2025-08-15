<template>

  <div class="com">
    <el-skeleton :type="type" :count="count" v-if="loading"/>
    <slot v-else/>
  </div>

</template>

<script>

  export default {
    props: {
      type: {
        default: 'text'
      },
      count: {
        default: 4
      },
      loading: {
        type: Boolean,
        default: false
      }
    },
  }

</script>

<style lang="scss" scoped>


</style>
