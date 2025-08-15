<template>

  <q-dialog v-model="visibleData">

    <q-card style="width: 600px;">

      <q-card-section class="row items-center q-px-lg">
        <div class="text-h6">Задать вопрос</div>
        <q-space/>
        <q-btn dense flat icon="close" round v-close-popup/>
      </q-card-section>

      <q-card-section class="q-pt-none q-px-lg">

        <q-form
          class="s-form-section-controls"
          ref="form"
        >
          <div class="row q-col-gutter-md">

            <div class="col-24">

              <q-input
                label="Ваше имя"
                outlined
                v-model="data.name"
              />

            </div>

            <div class="col-12">

              <q-input
                label="Телефон"
                lazy-rules
                mask="+# (###) ### - ######"
                outlined
                unmasked-value
                v-model="data.phone"
              />

            </div>

            <div class="col-12">

              <q-input
                label="E-mail"
                outlined
                v-model="data.email"
              />

            </div>

            <div class="col-24">

              <q-input
                :rules="[
                  val => !!val || 'Обязательное поле'
                ]"
                label="Вопрос"
                outlined
                type="textarea"
                v-model="data.message"
              />

            </div>

          </div>

        </q-form>

      </q-card-section>

      <q-card-actions align="right" class="q-px-lg q-pb-lg">
        <q-btn
          :disable="status=='loading' || status=='success'"
          :icon="$util.base.variants(status, {
            'success': 'fas fa-check',
            'error': 'fas fa-exclamation-circle',
          })"
          :loading="status==='loading'"
          @click="onSubmit"
          class="full-width"
          color="primary"
          label="Отправить"
        />
      </q-card-actions>

    </q-card>

  </q-dialog>

</template>

<script>
  import MDialogable from '@common/dialog/mixin/dialogable'

  const qs = require('querystring')

  import axios from 'axios'

  export default {
    mixins: [MDialogable],
    props: {
      entityNid: {}
    },
    components: {},
    data() {
      return {
        data: {
          name: null,
          phone: null,
          email: null,
          message: null,
          entityNid: this.entityNid
        },
        proc: false,
        status: ''
      }
    },
    methods: {
      onSubmit() {
        this.$refs.form.validate().then(async (success) => {
          if (success) {

            try {

              this.status = 'loading'

              let {data} = await axios.post('https://www.ogirk.ru/wp-json/conf/send-question', qs.stringify(this.data), {
                headers: {
                  'Content-Type': 'application/x-www-form-urlencoded'
                }
              })

              this.status = 'success'

              setTimeout(()=>{
                this.visibleData = false
                this.$q.notify('Сообщение успешно отправлено')
              }, 1000)

            } catch (e) {

              console.log(e)

              this.status = 'error'
            }

          }
        }).catch((e) => {

        })
      }
    },
    watch: {}
  }
</script>


<style lang="sass" scoped>

  .c-subjects
    td
      font-size: 16px

</style>
