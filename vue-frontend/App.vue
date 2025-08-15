<template>
  <div id="q-app">
    <div v-if="show">
      <router-view></router-view>
    </div>
    <div
      style="position:fixed; top:0; left:0; z-index: 1000; width:100%; height:100%; display: flex;"
      v-if="!isMounted"
    >
      <div style="margin: auto;">
        <img src="/statics/spinner.gif"/>
      </div>
    </div>
  </div>
</template>
<script>
  export default {
    name: 'App',
    data() {
      return {
        show: true,
        isMounted: true
      }
    },
    created() {

    },
    computed: {

    },
    mounted() {
      this.isMounted = true
      this.show = true
    },
    async preFetch(ctx) {
      return ctx.store.dispatch('fetchStateCommon')
    },
  }
</script>
