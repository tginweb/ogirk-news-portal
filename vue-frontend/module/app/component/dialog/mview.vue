<template>

  <q-dialog
    maximized
    persistent
    v-model="visible"
  >


    <q-card class="" ref="container">
      <q-bar>
        <q-space/>
        <q-btn dense flat icon="close" v-close-popup>
          <q-tooltip content-class="">Close</q-tooltip>
        </q-btn>
      </q-bar>

      <q-card-section class="q-pt-none">

        <component
          :is="payload.router.component()"
          :vroute="payload.router"
          context="instance"
          ref="com"
          v-if="payload.router.component"
        />

      </q-card-section>

    </q-card>

  </q-dialog>

</template>

<script>

  import CDialog from '@common/dialog/component/dialog'
  import {scroll} from 'quasar'

  const {getScrollTarget, setScrollPosition} = scroll

  export default {
    extends: CDialog,
    props: {
      model: {}
    },
    components: {},
    data() {
      return {
        dialogId: 'app/mview',
      }
    },
    created() {
    },
    mounted() {


    },
    computed: {

      routeFullPath() {
        return this.payload.router.fullPath
      }
    },
    methods: {},
    watch: {

      routeFullPath: {
        handler(val) {

          this.$nextTick(() => {

            this.$refs.container.$el.scrollTo(0, 0)

            this.$refs.com.preLoad();

          })
        },
        immediate: true
      }
    }
  }

</script>

<style lang="scss">


</style>
