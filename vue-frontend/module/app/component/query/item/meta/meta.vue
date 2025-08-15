<template>

  <div class="i-meta flex items-center">

    <div class="i-date">

    </div>

    <q-space></q-space>

    <div class="i-social q-gutter-x-sm">
      <el-share
        class="q-gutter-sm"
        :sharing="sharingData"
      ></el-share>
    </div>

  </div>

</template>

<script>

  export default {
    props: {
      item: {},
    },
    computed: {
      sharingData() {
        return {
          url: this.item.url,
          title: this.itemTitle,
          description: this.item.description,
        }
      }
    }
  }
</script>

<style lang="scss" scoped>

  .i-meta {
    color: #a1a1a1;
    text-shadow: 0 0 2px rgba(0, 0, 0, 0.5);
  }

  .i-date {
    font-size: 13px;
  }

  .i-social {
    font-size: 14px;
  }

</style>
