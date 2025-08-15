<template>

  <div class="query-item">

    <q-page class="i-page q-py-xl" v-bind="bindPage">

      <div class="i-content bg-white q-ml-auto q-mr-auto q-pa-lg">

        <template v-if="step === 'start'">

          <div class="i-step-start  text-center">

            <h1 class="q-ma-none q-mb-md s-font-3xl text-weight-bold s-lh-normal ">{{item.title}}</h1>

            <div class="s-font-lg" v-html="item.contentFormatted"/>

            <q-btn
              @click="onStart"
              class="q-mt-lg"
              color="secondary"
              label="Начать тест"
              unelevated
            />

          </div>

        </template>
        <template v-else-if="step === 'process'">

          <div class="i-step-process ">

            <CQuestion
              :questionIndex="questionIndex"
              @next="onNext"
              class="i-question"
              v-model="currentQuestion"
            />

          </div>

        </template>
        <template v-else-if="step === 'result'">

          <div class="i-result ">

            <div v-bind="bindResult" class="i-result-cover q-px-lg q-py-xl">

              <div class="i-result-cover-overlay"></div>

              <div class="i-result-cover-info q-pa-md">

                <div class="flex items-center no-wrap q-gutter-lg">

                  <div class="i-result-score s-font-xxl text-bold">
                    {{answersCorrectCount}}/{{questions.length}}
                  </div>

                  <div class="i-result-text">

                    <div class="s-font-xxl text-bold">
                      {{answersResult.title}}
                    </div>

                    <div class="s-font-xl">
                      {{answersResult.text}}
                    </div>


                  </div>

                </div>


              </div>

            </div>


          </div>

        </template>

        <div class="q-mt-xl flex">

          <div v-if="step==='result'">

            <q-btn :to="item.url" color="primary" @click="onRepeat" label="сыгать еще раз" outline/>

          </div>

          <div class="i-result-share q-ml-auto flex items-center q-ml-auto " :class="{'q-mr-auto': step!=='result'}">

            <div class="q-mr-md">Поделиться</div>

            <el-share
              :sharing="item.share"
              :showLabels="false"
              class="q-gutter-sm"
              orientation="horizontal"
            />

          </div>

        </div>

      </div>

    </q-page>

  </div>

</template>

<script>
  import CItem from './../../_item-post'
  import CLayout from '~module/app/layout/main/page/1cols'
  import CQuestion from './parts/question'

  import {scroll} from 'quasar'

  const {getScrollTarget, setScrollPosition} = scroll

  export default {
    extends: CItem,
    components: {
      CLayout,
      CQuestion
    },
    apollo: {},
    data() {
      return {
        step: 'start',
        questionIndex: 0,
        questions: [],
        answers: [
          {score:0},
          {score:1},
          {score:0},
          {score:0},
          {score:0},
        ]
      }
    },
    methods: {
      prepare() {
        this.step = 'start'
        this.questionIndex = 0
        this.questions = this.$util.base.cloneDeep(this.item.meta.quiz_questions)
        // this.answers = []
      },

      onStart() {
        this.step = 'process'
      },
      onNext(answer) {

        this.answers.push(answer)

        if (this.questionIndex < this.questions.length - 1) {
          this.questionIndex++
        } else {
          this.step = 'result'
        }

        setScrollPosition(window, 0)
      },
      onRepeat() {
        this.prepare()
      },
    },
    computed: {
      currentQuestion() {
        return this.questions[this.questionIndex]
      },

      bindPage() {
        const res = {
          style: {}
        }

        res.style.backgroundImage = 'url(https://www.ogirk.ru' + this.item.meta.quiz_bg_image + ')'

        return res
      },

      bindResult() {
        const res = {
          style: {}
        }

        res.style.backgroundImage = 'url(' + this.item.meta.quiz_bg_image + ')'

        return res
      },

      answersScore() {
        return this.answers.reduce((summ, item) => (summ = summ + item.score, summ), 0)
      },

      answersCorrectCount() {
        return this.answers.reduce((summ, item) => (summ = summ + (item.score ? 1 : 0), summ), 0)
      },

      answersResult() {
        return this.item.meta.quiz_results.find(result => {
          return this.answersScore >= result.from && this.answersScore <= result.to
        })
      },
    },
    watch: {
      item: {
        immediate: true,
        handler: function (item) {
          if (item) {
            this.prepare();
          }
        }
      },
    },
  }
</script>

<style lang="scss" scoped>

  .i-page {
    background-size: cover;
  }

  .i-content {
    max-width: 600px;
    border: 1px solid #CCC;
    border-radius: 10px;
  }

  .i-question {

  }


  .i-result-cover {
    position: relative;
    background-position: center center;
    background-size: cover;
    min-height: 300px;
  }

  .i-result-cover-overlay {
    position: absolute;
    content: "";
    top: 0px;
    left: 0px;
    width: 100%;
    height: 100%;
    background-color: black;
    opacity: 0.65;
    z-index: 5;
  }

  .i-result-cover-info {
    background-color: #FFF;
    z-index: 10;
    position: relative;
  }

  .i-result-share {


    /deep/ {
      .c-item {
        border: 1px solid #ccc;
        padding: 6px;
        line-height: 1;
        display: inline-block;
        border-radius: 50%;
        font-size: 10px;

        .c-item-icon {
          color: #BBB;
          font-size: 16px;
        }
      }
    }
  }

</style>
