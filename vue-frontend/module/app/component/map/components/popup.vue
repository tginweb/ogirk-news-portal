<template>

  <div class="com">

    <div class="c-info">

      <h1
        class="c-title text-black text-weight-bold s-font-sm s-lh-normal q-ma-none q-px-md q-pt-md q-pb-sm"
        @click="$emit('select')"
      >
        {{entity.name}}
      </h1>

    </div>

  </div>

</template>

<script>

  export default {
    props: {
      entity: {}
    },
    data() {
      return {}
    },
  }

</script>

<style lang="scss" scoped>

  .com {
    max-width: 260px;
  }

  a {
    text-decoration: none;
  }

  .c-image {
    background-size: cover;
    height: 100px;
  }


  .c-type {
    margin-bottom: 8px;
    color: #ff4641;
    font-size: 13px;
    font-weight: 500;
    line-height: 1;
    letter-spacing: .464286px;
    text-transform: uppercase;
  }


  .c-region {
    color: #212121;
    margin-top: 12px;
    font-size: 15px;
  }

</style>
