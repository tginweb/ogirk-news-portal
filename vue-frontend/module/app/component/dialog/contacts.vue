<template>

  <q-dialog
    v-model="visible"
  >

    <q-card class="q-px-sm" ref="container" style="width: 400px;">

      <q-card-section class="row items-center">
        <div class="text-h6">Связаться с редакцией</div>
        <q-space/>
        <q-btn dense flat icon="close" v-close-popup>
          <q-tooltip content-class="">Close</q-tooltip>
        </q-btn>
      </q-card-section>


      <q-card-section class="q-pt-sm q-pb-lg">

        <div class="q-gutter-y-md">

          <q-btn
            type="a"
            target="_blank"
            :icon="$icons.fabWhatsapp"
            label="WhatsApp"
            href="https://api.whatsapp.com/send?phone=+79016543781"
            class="full-width"
            unelevated
            style="background-color:#f8f5f2;font-size: 18px;"
          />

          <q-btn
            type="a"
            target="_blank"
            :icon="$icons.fasPhoneAlt"
            label="+7 (3952) 20-06-28"
            href="tel:73952200628"
            class="full-width"
            unelevated
            style="background-color:#f8f5f2;font-size: 18px;"
          />

          <q-btn
            type="a"
            target="_blank"
            :icon="$icons.farEnvelope"
            label="reklama@ogirk.ru"
            href="mailto:reklama@ogirk.ru"
            class="full-width"
            unelevated
            style="background-color:#f8f5f2;font-size: 18px;"
          />

        </div>

      </q-card-section>

    </q-card>

  </q-dialog>

</template>

<script>

  import CDialog from '@common/dialog/component/dialog'

  export default {
    extends: CDialog,
    props: {
      model: {}
    },
    components: {},
    data() {
      return {
        dialogModule: 'app',
        dialogName: 'contacts',
      }
    },
    created() {
    },
    mounted() {


    },
    computed: {

    },
    methods: {},
    watch: {


    }
  }

</script>

<style lang="scss">


</style>
