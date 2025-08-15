<template>
  <div class="query-item" v-bind="bind">
    <div class="i-content">
---
      {{item}}
    </div>
  </div>
</template>

<script>
  import CItem from './../_item-post'

  export default {
    name: 'item-2',
    extends: CItem,
    components: {

    },
    data() {
      return {
      }
    },
    methods: {
    }
  }
</script>

<style lang="scss" scoped>

  .query-item {

  }

  .i-content {
    border: 1px solid #e8e8e8;
    border-radius: 4px;
    height: 100%;

    display: flex;
    flex-direction: column;
  }

  .i-media {
    position: relative;

    color: #fff;
    a {
      color: #fff;
    }
  }

  .i-info {
    width: 100%;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
  }

  .i-thumb {
    line-height: 0;
  }

  .i-thumb-image {
    max-width: 100%;
    height: auto;
  }

  .i-title {
    font-weight: 600;
    line-height: 1.4em;
  }

  .i-meta {
    margin-top: auto;
    color: #a1a1a1;
    font-size: 14px;
  }

</style>
