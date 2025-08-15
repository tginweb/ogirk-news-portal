<template>

  <q-layout view="lHh Lpr lFf">

    <CSystem/>


    <component :is="comHeader" ref="header"/>

    <q-page-container>

      <transition appear mode="out-in" name="fade">
        <router-view/>
      </transition>

    </q-page-container>

    <CFooter/>

  </q-layout>

</template>

<script>
  import CSystem from '~module/app/layout/.shared/system'

  import CHeaderDesktop from '~module/app/layout/main/header/header-desktop'
  import CHeaderMobile from '~module/app/layout/main/header/header-mobile'

  import CFooter from '~module/app/layout/main/footer/footer'
  import CSeo from '~module/seo/component/links-bottom'

  export default {
    components: {
      CSystem,
      CHeaderDesktop,
      CHeaderMobile,
     // 'c-header-special-desktop': () => import('~module/app/layout/main/header/header-special-desktop'),
     // 'c-header-special-mobile': () => import('~module/app/layout/main/header/header-special-mobile'),
      CFooter,
      CSeo
    },
    data() {
      return {
        leftDrawerOpen: false,
        scrollPosition: 0,
      }
    },
    computed: {

      seoShow() {

        if (this.$route.meta.seoBottomHide) {
          return false
        }

        if (['/2021/02/03/evtushenkov-vladimir-prodolzhaet-vydeljat-sredstva-na-borbu-s-pandemiej'].indexOf(this.$route.path.replace(/\/$/, ""))>-1) {
          return false
        }

        return true
      },

      comHeader() {

        let name;

        if (this.$store.state.app.siteMode === 'special') {
          name = this.isDesktop ? 'CHeaderSpecialDesktop':'CHeaderSpecialMobile'
        } else {
          name = this.isDesktop ? 'CHeaderDesktop':'CHeaderMobile'
        }

        return name
      }
    },
    methods: {


    }
  }
</script>

<style lang="scss">
  div {

  }


</style>
