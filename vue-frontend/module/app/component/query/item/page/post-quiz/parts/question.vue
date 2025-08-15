<template>
  <div class="com">

    <div class="c-question-text s-font-xl" v-html="question.text" v-if="question.text"/>

    <div class="c-question-image q-mt-md" v-if="question.image">
      <img v-lazy="'https://www.ogirk.ru' + question.image">
    </div>

    <q-list bordered class="c-question-variants q-mt-md" separator>

      <q-item
        :key="variantIndex"
        @click="onVariantClick(variantIndex)"
        class="c-question-variant"
        clickable
        v-bind="bindVariant(variant)"
        v-for="(variant, variantIndex) of answers"
      >
        <q-item-section
          class="s-font-lg"
        >
          <q-item-label
            class="c-variant-text"
          >
            {{variant.text}}
          </q-item-label>

          <q-item-label
            class="c-variant-answer q-pt-sm"
            v-if="variant.status !== 'none'"
          >

            <template v-if="variant.status === 'answered-correct' || variant.status === 'correct'">

              <div class="c-variant-answer-comment">

                <span
                  class="text-green-6"
                  v-if="variant.status === 'answered-correct'"
                >
                  Верно!
                </span>

                {{variant.answer_comment}}

              </div>

            </template>
            <template v-else>

              <div class="c-variant-answer-comment">

                <div class="c-variant-answer-comment">

                <span
                  class="text-red-6"
                >
                  Не верно!
                </span>

                  {{variant.answer_comment}}

                </div>


              </div>

            </template>

          </q-item-label>

        </q-item-section>

      </q-item>

    </q-list>

    <q-btn
      @click="onNext"
      class="full-width q-mt-lg"
      color="primary"
      label="Дальше"
      unelevated
      v-if="answered"
    />

  </div>
</template>

<script>

  export default {
    props: {
      value: {},
      questionIndex: {}
    },
    data() {
      return {
        valueInternal: this.value,
        userAnswers: []
      }
    },
    watch: {
      questionIndex(val) {
        this.valueInternal = this.value
        this.userAnswers = []
      },
    },
    methods: {
      onNext() {
        const answer = {
          score: this.answeredCorrect ? this.answeredVariants.reduce((summ, obj) => (summ = summ + parseInt(obj.score), summ), 0) : 0
        }

        this.$emit('next', answer)
      },

      onVariantClick(index) {

        // this.userAnswers = [index]; return;

        if (!this.answered) {
          this.userAnswers.push(index)
        }
      },

      getVariantStatus(variantIndex) {

        if (this.answered) {
          if (this.userAnswers.indexOf(variantIndex) > -1) {
            if (this.valueInternal.answers[variantIndex].correct) {
              return 'answered-correct'
            } else {
              return 'answered-incorrect'
            }
          } else {
            if (this.valueInternal.answers[variantIndex].correct) {
              return 'correct'
            }
          }
        }

        return 'none'
      },

      bindVariant(variant) {
        const res = {
          class: {}
        }

        res.class['status-' + variant.status] = true;

        return res
      }
    },
    computed: {

      answers() {
        return this.question.answers.map((item, index) => {
          return {
            ...item,
            status: this.getVariantStatus(index)
          }
        })
      },

      question() {
        return this.valueInternal
      },

      correctVariants() {
        return this.valueInternal.answers.filter(item => !!item.correct)
      },

      answeredVariants() {
        return this.userAnswers.map(index => this.valueInternal.answers[index])
      },

      answered() {
        return this.answeredVariants.length === this.correctVariants.length;
      },

      answeredCorrect() {
        return !this.answeredVariants.find(item => !item.correct)
      }
    }
  }
</script>

<style lang="scss" scoped>

  .c-question-text {

  }

  .c-question-image {
    img {
      max-width: 100%;
      height: auto;
    }
  }

  .c-question-variants {
    border-radius: 8px;
  }

  .c-question-variant {

    &.status-correct,
    &.status-answered-correct {
      border-left: 10px #1ca850 solid;
    }

    &.status-answered-incorrect {
      border-left: 10px #ad3018 solid;
    }
  }
</style>
