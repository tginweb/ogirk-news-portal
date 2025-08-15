<template>

  <div class="com">

    <CAdminMenu v-if="adminMenuOpened" />

    <component
      :is="dialog"
      :key="dialog"
      v-for="(dialog, index) of allDialogs"
      v-if="$store.getters['dialogVisible'](dialog.split('__').join('/'))"
      v-bind="$store.getters['dialogProps'](dialog.split('__').join('/')) || {}"
    />

    <Keypress
      v-for="keypressEvent in keypressEvents"
      :key="keypressEvent.id"
      :key-event="keypressEvent.keyEvent"
      :multiple-keys="keypressEvent.multipleKeys"
      @success="onKeypress"
    />

  </div>

</template>

<script>

  import * as dialogs from '~module/app/component/dialog'

  import CAdminMenu from './admin-menu.vue'

  let allDialogs = [];

  Array.prototype.push.apply(allDialogs, Object.keys(dialogs));

  export default {
    components: {
      ...dialogs,
      Keypress: () => import('vue-keypress'),
      CAdminMenu
    },
    data() {
      return {
        keypressEvents: [
          {
            keyEvent: 'keydown',
            multipleKeys: [
              {
                keyCode: 69, // A
                modifiers: ['altKey'],
                preventDefault: true,
              },
            ],
          },
        ],
        adminMenuOpened: false
      }
    },
    mounted() {
      this.$bus.on('loadingStart', this.onLoadingStart);
      this.$bus.on('loadingStop', this.onLoadingStop);
    },
    beforeDestroy() {
      this.$options.allDialogs = null
      this.$bus.off('loadingStart', this.onLoadingStart);
      this.$bus.off('loadingStop', this.onLoadingStop);
    },
    methods: {
      onLoadingStart(opt) {
        console.log('LOADING')
        this.$q.loading.show(opt)
      },
      onLoadingStop() {
        this.$q.loading.hide()
      },
      onKeypress(r) {

        this.adminMenuOpened = !this.adminMenuOpened

      }
    },
    computed: {
      allDialogs() {
        return allDialogs
      }
    }
  }

</script>

<style lang="scss">


</style>
