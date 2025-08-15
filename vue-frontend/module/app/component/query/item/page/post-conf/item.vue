<template>
  <div class="item-page-post-post item-page-post-post-2col">


    <c-dialog-send
      :visible.sync="dialog.show"
      :entityNid="item.nid"
      v-if="dialog.show"
    />

    <div class="container">

      <div class="c-header q-pb-lg q-mb-lg">

        <CMetaInline
          :created="itemDateFormatted"
          :item="item"
          :share="shareData"
          :termsCategory="termsCategory"
          :termsTags="termsTags"
          class="q-mb-md"
        />

        <h1 class="c-header-title s-font-xxl s-font-md-4xl q-ma-none s-align-center ">
          {{itemTitle}}
        </h1>

      </div>

      <div class="row q-col-gutter-x-xl q-col-gutter-y-md">

        <div class="col-17">

          <div v-if="queries.questions.result">

            <div
              :key="group.nid"
              :ref="'group-' + group.nid"
              class="c-question-group q-mb-lg"
              v-for="group of questionsGroups"
            >

              <div class="c-question-group-title s-font-3xl text-center q-mb-md">
                {{group.name}}
              </div>

              <div class="c-question-group-children">

                <CQuestion
                  :item="node"
                  :key="node._id"
                  :ref="'block-' + index"
                  :search="filter.text"
                  :showCat="questionsMode!=='grouped'"
                  class="c-question q-mb-xl"
                  v-for="node of group.children"
                />

              </div>

            </div>

          </div>

        </div>

        <div class="col-7" id="col-side">

          <div class="c-social q-mb-lg flex items-center">

            <div>
              Поделиться:
            </div>

            <el-share
              :sharing="shareData"
              class="q-gutter-sm q-ml-auto"
            />

          </div>

          <div
            class="q-mb-xl"
            v-hc-sticky="{stickTo: '#col-side', followScroll: true, top: 50}"
          >

            <div class="c-menu q-mb-md" v-if="questionsCats.length">

              <ol class="">
                <li
                  :key="term._id"
                  @click="onNavGroup(term)"
                  class="q-mb-md s-cursor-pointer"
                  v-for="term of questionsCats"
                >
                  {{term.name}}
                </li>
              </ol>

            </div>

            <div class="c-search q-mb-lg">

              <q-input
                clearable
                dense
                label="Поиск по вопросам"
                v-model="filter.text"
              />

            </div>

            <div class="c-send q-mb-md">

              <q-btn
                class="full-width"
                color="primary"
                label="Задать свой вопрос"
                @click="dialog.show = true"
                outlined
                rounded
                unelevated
                v-model="filter.text"
              />

            </div>

          </div>

        </div>

      </div>

    </div>

  </div>
</template>

<script>
  import CItem from './../../_item-post'
  import CSidebar from '~module/app/layout/main/sidebar/article'
  import CMetaInline from './parts/meta-inline'
  import CQuestion from './parts/question'
  import CDialogSend from './parts/dialog-send'

  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";
  import {dom, scroll} from 'quasar'

  const {getScrollTarget, setScrollPosition} = scroll

  const {height, width} = dom

  export default {
    components: {
      CSidebar,
      CMetaInline,
      CQuestion,
      CDialogSend
    },
    extends: CItem,
    props: {},
    apollo: {
      questions: generateQueryInfo('questions', require('~module/entity/graphql/getPosts.gql'), {}),
    },

    data() {
      return {
        queries: {
          questions: {
            vars: {
              filter: {
                type: 'sm-qa-question',
                parentNid: this.item.nid,
              },
              nav: {
                limit: 2000
              },
              imageSize: 't1.5',
              termsTaxonomy: ['sm-qa-question-cat']
            },
            result: null
          },
        },
        questionsMode: 'grouped',
        filter: {
          text: ''
        },
        dialog: {
          show: false
        }
      }
    },
    computed: {
      questionsCats() {
        const nodes = this.queries.questions.result ? this.queries.questions.result.nodes : []
        const groups = nodes.reduce((map, obj) => {
          obj.terms.forEach(term => map[term.nid] = term);
          return map
        }, {})
        delete groups[0]
        return Object.values(groups)
      },

      questionsFiltered() {
        return (this.queries.questions.result && this.queries.questions.result.nodes || []).filter(item => {

          if (this.filter.text) {
            const regExp = new RegExp(this.filter.text, "ig");

            const text = item.meta.question + ' ' + item.meta.answer

            if (text.search(regExp) === -1) return false
          }

          return true
        })
      },

      questionsGroups() {
        return this.questionsFiltered.reduce((map, obj) => {

          let cat, catNid

          if (obj.terms.length) {
            cat = obj.terms[0]
          } else {
            cat = {
              nid: 0,
              name: ''
            }
          }

          if (!map[cat.nid]) map[cat.nid] = {
            ...cat,
            children: []
          }

          map[cat.nid].children.push(obj)

          return map
        }, {})
      },
    },
    methods: {
      onNavGroup(term) {
        const block = this.$refs['group-' + term.nid][0]
        const target = getScrollTarget(block)
        const offset = block.offsetTop
        const duration = 300
        setScrollPosition(target, offset, duration)
      }
    },
  }
</script>

<style lang="scss" scoped>

  .c-menu {

    padding: 20px 15px 19px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #fff;
    overflow-x: hidden;

    ol {
      counter-reset: list 0;
      list-style: none;
      padding: 0;
      margin: 0;
    }

    li {
      display: flex;
      flex-flow: row nowrap;
      font-weight: 600;
      font-size: 16px;
      font-family: Proxima Nova, Arial, Helvetica Neue, sans-serif;
      line-height: 22px;

      &:last-child {
        margin-bottom: 0;
      }

      &:before {
        min-width: 29px;
        color: #a32f2f;
        font-weight: 700;
        font-size: 16px;
        line-height: 22px;
        content: counter(list) ". ";
        counter-increment: list;
      }
    }
  }


  .c-search {

  }

  .c-question-group {
    .c-question-group-title {
      font-weight: bold;
    }
  }


  .c-social {


    /deep/ {
      .c-item {
        border: 1px solid #ccc;
        padding: 9px;
        line-height: 1;
        display: inline-block;
        border-radius: 50%;
        font-size: 10px;

        .c-item-icon {
          color: #BBB;
          font-size: 18px;
        }
      }
    }
  }
</style>
