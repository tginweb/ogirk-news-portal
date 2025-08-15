<template>
  <div
    :id="'ad-teaser-zone-' + demo.slug"
    class="com"
    :class="{
     'demo-selected': isSelected
   }"

  >

    <div class="i-content text-center text-white flex items-center  bg-grey-8">

      <div
        :style="{
         fontSize: '100px'
        }"
        class="i-title text-weight-bold q-mx-auto q-my-auto"
      >
        {{demo.slug}}
      </div>

    </div>

  </div>
</template>

<script>

  export default {

    props: {
      demo: {}
    },

    computed: {
      isSelected() {
        return this.$store.state.ad.adDemoTeaserZone === this.demo.slug
      },
    }

  }
</script>

<style lang="scss" scoped>

  .com {
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 100%;
    z-index: 200;

    &.demo-selected {

      .i-content {
        background-color: #ff2222 !important;
      }
    }

    .i-content {
      min-height: 100%;
    }

  }

</style>
