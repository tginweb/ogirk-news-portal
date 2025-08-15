<template>
  <div class="com s-font-md">

    <div class="c-top q-px-lg q-py-md">
      <div
        class="c-question sm-content-style"
        v-html="textQuestion"
      />
    </div>

    <div class="c-bottom q-px-lg q-pb-lg q-mt-md">

      <div
        class="c-answer-author q-mb-md"
        v-if="item.meta.answer_author"
      >
        {{item.meta.answer_author}}:
      </div>

      <div
        class="c-answer sm-content-style"
        v-html="textAnswer"
      ></div>

      <div
        class="c-cats q-mt-sm text-right"
        v-if="showCat && item.terms.length"
      >
        <q-btn
          :key="term._id"
          :label="'#' + term.name"
          class=""
          color="accent"
          dense
          flat
          v-for="term of item.terms"
        />
      </div>

    </div>

  </div>
</template>

<script>

  export default {
    props: {
      item: {},
      showCat: {default: true},
      search: {}
    },
    data() {
      return {}
    },
    computed: {
      textQuestion() {
        return this.highlight(this.item.meta.question)
      },
      textAnswer() {
        return this.highlight(this.$util.base.htmlTrim(this.item.meta.answer))
      },
    },

    methods: {
      highlight(text) {

        if (this.search) {
          text = text.replace(new RegExp('(' + this.search + ')'), '<span class="highlighted">$1</span>')
        }

        return text;
      }
    }
  }
</script>

<style lang="scss" scoped>

  /deep/ .highlighted {
    background-color: #ff8f00;
  }

  .com {
    border-radius: 8px;
    border: 1px solid #ddd;

    .c-question {
      font-weight: normal !important;
      font-style: normal;

      /deep/ {
        strong, b, em {
          font-weight: normal !important;
          font-style: normal !important;
        }
      }
    }

    .c-answer-author {
      font-style: italic;
    }

    .c-answer {

    }
  }

  .c-top {
    background-color: #ecf0f1;
  }

  .c-bottom {

  }

</style>
