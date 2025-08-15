<template>

  <div class="q-py-md">

    <div class="row">

      <div class="col-4">

        <component
          :class="{
            'fit': params.childsScroll
          }"
          :is="params.childsScroll ? 'q-scroll-area':'div'"
          style="min-height: 120px;"
          visible
        >
          <q-list style="">
            <q-item
              :key="index"
              :to="item.url"
              @click="onItemClick(item)"
              @mouseenter="onItemOver(item)"
              clickable
              v-close-popup
              v-for="(item, index) in node.children"
            >
              <q-item-section>
                {{item.title}}
              </q-item-section>
            </q-item>
          </q-list>

        </component>

      </div>

      <div class="col-20">

        <transition name="fade">

          <div class="q-px-md full-height flex">

            <el-spinner
              :count="3"
              class="q-my-auto q-mx-auto full-width"
              v-if="queries.list.state.isLoading "
            />

            <query-items-grid
              :item="{
                is: 'query-item-2',
                class: 'col-md-6',
                elements: {
                  meta: {
                    enable: false
                  },
                  content: {
                    class: 'q-py-md s-border-bottom'
                  },
                  title: {
                    defclass: 's-font-sm'
                  }
                }
              }"
              :items="queries.list.result.nodes"
              rowClass="q-col-gutter-md"
              v-else-if="queries.list.result"
            />

          </div>

        </transition>

      </div>

    </div>

  </div>

</template>

<script>
  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";

  export default {
    props: {
      node: {},
      value: {},
      params: {
        default: () => ({})
      }
    },
    apollo: {
      list: generateQueryInfo('list', require('~module/entity/graphql/getPosts.gql'), {fetchPolicy: 'cache-first'}, {
        varPath: 'cqueries.list.vars',
      }),
    },
    data() {
      return {
        itemOverTimout: null,
        catId: this.node.entityNid,
        queries: {
          list: {
            result: null,
            state: {isLoading: false}
          },
        }
      }
    },
    watch: {},
    computed: {
      cqueries() {
        return {
          list: {
            vars: {
              filter: {
                type: 'post',
                tax: [
                  {taxonomy: "category", id: this.catId, withChildren: true}
                ],
                haveImage: true,
              },
              nav: {
                limit: 4
              },
              imageSize: 't1.78'
            },
            result: null
          },
        }
      }
    },
    methods: {

      onItemClick(item) {
        /*
        if (this.params.childFetchEvent === 'click') {
          this.fetchChild();
        } else {
          this.$emit('close')
        }

         */

        this.$emit('close')
      },

      onItemOver(item) {
        if (this.params.childFetchEvent !== 'click') {
          this.fetchChild(item);
        }
      },

      fetchChild(item) {

        if (this.itemOverTimout) clearTimeout(this.itemOverTimout)

        this.itemOverTimout = setTimeout(() => {
          //this.queries.list.result = null

          this.$nextTick(() => {
            this.catId = item.entityNid
          })
        }, 200)
      }
    }
  }
</script>

<style lang="scss" scoped>

  .com {

  }

</style>
