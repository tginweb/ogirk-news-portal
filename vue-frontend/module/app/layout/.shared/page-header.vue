<template>

  <div class="com">

    <div class="flex items-center no-wrap">

      <h1 v-html="title" class="s-font-xl s-font-sm-4xl"></h1>

      <div class="q-ml-auto">
        <slot name="side"></slot>
      </div>

    </div>

    <el-submenu class="q-mt-sm " v-if="showSubmenu"/>

  </div>

</template>

<script>

  export default {
    props: {
      title: {},
      showSubmenu: {default: true}
    },
  }

</script>

<style lang="scss" scoped>

  .com {
    border-top1: 4px solid #aab0b6;

    h1 {
      line-height: 1.6em;
      margin: 0 0 0px 0;
      font-weight: 600;
      color: #818a93;
      text-transform: uppercase;
    }
  }

</style>
