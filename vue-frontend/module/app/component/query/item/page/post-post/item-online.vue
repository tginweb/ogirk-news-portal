<template>
  <div class="item-page-post-post item-page-post-post-2col">

    <div class="container">

      <div class="row q-col-gutter-x-lg q-col-gutter-y-md">

        <div class="col-24">

          <div class="c-header q-pb-lg q-mb-lg">

            <CMetaInline
              class="q-mb-md"
              :item="item"
              :share="shareData"
              :termsCategory="termsCategory"
              :termsTags="termsTags"
              :created="itemDateFormatted"
            />

            <h1 class="c-header__title s-font-xxl s-font-md-4xl q-ma-none s-align-center ">
              {{itemTitle}}
            </h1>

          </div>

          <div class="c-body__main" ref="bodyMain">

            <div class="flex q-mb-lg" v-if="item.contentFormatted">

              <div
                class="c-body__content sm-content-style q-mx-auto"
                style="max-width: 700px;"
                v-html="item.contentFormatted"
              />

            </div>

            <q-btn
              :label="'Новых сообщений: ' + notesCollapsed.length"
              class="full-width q-mb-lg"
              @click="onNotesExpand"
              size="xl"
              color="primary"
              v-if="notesCollapsed.length"
            />

            <div class="q-pa-none q-pa-md-lg flex" :class="{'bg-grey-3':screen.gt.md}">

              <template v-if="notesExpanded.length">

                <query-items-grid
                  :item="{
                    class: 'col-24',
                    is: 'query-item-note',
                    elements: {}
                  }"
                  :items="notesExpanded"
                  class="c-notes q-ml-auto q-mr-auto"
                  rowClass="q-col-gutter-lg"
                />

              </template>

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
  import CMediaHeader from './hero/hero-media'
  import CMetaInline from './meta/meta-inline'

  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";
  import {dom} from 'quasar'

  const {height, width} = dom

  export default {
    components: {
      CSidebar,
      CMediaHeader,
      CMetaInline,
    },
    extends: CItem,
    props: {},
    apollo: {
      notes: generateQueryInfo('notes', require('~module/entity/graphql/getPosts.gql'), {}),

      $subscribe: {
        notes: {
          query: require('~module/app/graphql/noteAdded.gql'),
          result({data}) {

            if (!this.queries.notes.result.nodes) {
              this.queries.notes.result.nodes = [];
            }

            const node = {
              ...data.res,
              isnew: true
            }

            this.queries.notes.result.nodes.unshift(node)
          },
        },
      },
    },

    data() {
      return {
        queries: {
          notes: {
            vars: {
              filter: {
                type: 'sm-note',
                parentNid: this.item.nid,
              },
              nav: {
                limit: 200
              },
              imageSize: 't1.5'
            },
            result: null
          },
        },
      }
    },
    computed: {

      notesExpanded() {
        return this.queries.notes.result && this.queries.notes.result.nodes.filter(node => !node.isnew) || []
      },

      notesCollapsed() {
        return this.queries.notes.result && this.queries.notes.result.nodes.filter(node => !!node.isnew) || []
      },
    },
    methods: {
      onNotesExpand() {
        this.queries.notes.result.nodes.forEach(node => {
          node.isnew = false
        })
      }
    },
    mounted() {

    }
  }
</script>

<style lang="scss" scoped>

  .c-notes {
    max-width: 650px;
  }

</style>
