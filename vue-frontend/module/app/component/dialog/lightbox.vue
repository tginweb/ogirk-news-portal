<template>

  <q-dialog v-model="visible" >

    <div ref="dailogContent" style="width: auto; max-width: 95vw; background: transparent;box-shadow:none;">

      <div class="flex">
        <q-space/>
        <q-btn color="white" dense flat icon="close" round size="25px" style="" @click="visible=false"/>
      </div>

      <div class="q-mt-md">

        <div class="c-wrap text-center">

          <div class="c-image"><img :src="url"/></div>

          <div class="c-caption q-mt-md" v-if="caption">{{caption}}</div>

        </div>

      </div>

    </div>

  </q-dialog>

</template>

<script>
  import CDialog from '@common/dialog/component/dialog'

  export default {
    extends: CDialog,
    props: {
      url: {},
      caption: {}
    },
    components: {},
    data() {
      return {
        dialogModule: 'app',
        dialogName: 'lightbox',
      }
    },
  }

</script>

<style lang="scss" scoped>

  .c-wrap {
    position: relative;
  }

  .c-caption {
    color: #fff;
    max-width: 85vw;
  }

</style>
