<template>
  <div>
    <component :is="component"/>
  </div>
</template>

<script>

  import CPage_default from '~com/pages/front/default'
  import CPage_feed from '~com/pages/front/feed'
  import CPage from "~module/app/component/route";

  export default {
    name: 'frontpage',
    extends: CPage,
    components: {
      CPage_default,
      CPage_feed
    },
    apollo: {

    },
    data() {
      return {
        page: {
          title: 'Областная газета'
        }
      }
    },
    computed: {
      component() {
        return 'CPage_' + this.$store.state.app.options.frontpageMode
      }
    }
  }
</script>
