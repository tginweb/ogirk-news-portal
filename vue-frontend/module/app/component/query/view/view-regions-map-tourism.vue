<template>

  <el-widget
    :bodyOnly="widget.bodyOnly"
    :loading="loading"
    :moreLabel="widget.moreLabel"
    :moreUrl="widget.moreUrl"
    :skeleton="widget.skeleton"
    :title="widget.title"
    v-if="query.result"
  >
    <template v-slot:default>

      <template v-if="screen.gt.md">

        <div class="row">

          <div class="col-4">

            <q-scroll-area
              :style="{height: height}"
              class="places-list"
              visible
            >
              <div
                :class="{
                  'active': entity.nid === activeNid
                }"
                :key="entity.nid"
                @click.capture.prevent.stop="onListClick(entity)"
                class="places-list__item"
                v-for="entity of query.result.nodes"
              >
                <router-link
                  :to="entity.url"
                  class="s-link text-accent"
                >
                  {{entity.name}}
                </router-link>
              </div>
            </q-scroll-area>

          </div>

          <div class="col-9" v-if="activeEntity">

            <q-scroll-area
              :style="{height: height}"
            >
              <div class="q-px-lg">

                <div class="s-font-lg q-mb-xs text-weight-bold">{{activeEntity.name}}</div>

                <q-spinner
                  :thickness="2"
                  color="primary"
                  size="3em"
                  v-if="activeIrkipediaLoading"
                />
                <template v-else>

                  <div v-if="activeIrkipedia">

                    <div class="">

                      <div
                        v-html="activeIrkipedia.teaser.substring(0, 500) + '...'"
                      />

                    </div>

                  </div>

                </template>

                <div class="menu-links flex q-gutter-y-sm q-gutter-x-md q-pt-xs">

                  <a :href="activeIrkipedia.url" class="menu-links__item s-link-wrapper text-accent" target="_blank"
                     v-if="activeIrkipedia">
                    <q-icon name="fas fa-caret-right"/>
                    <span class="s-link">статья на irkipedia.ru</span>
                  </a>

                </div>


              </div>
            </q-scroll-area>

          </div>

          <div class="col-auto col-grow">

            <CMap
              :entities="query.result.nodes"
              :height="height"
              v-model="activeNid"
            />

          </div>

        </div>

      </template>
      <template v-else>

        <div class="row q-col-gutter-sm">

          <div class="col-9">

            <q-scroll-area
              :style="{height: height}"
              class="places-list"
              visible
            >
              <div
                :class="{
                'active': entity.nid === activeNid
              }"
                :key="entity.nid"
                @click.capture.prevent.stop="onListClick(entity)"
                class="places-list__item"
                v-for="entity of query.result.nodes"
              >
                <router-link
                  :to="entity.url"
                  class="s-link text-accent s-font-xs"
                >
                  {{entity.name}}
                </router-link>
              </div>
            </q-scroll-area>

          </div>

          <div class="col-auto col-grow" v-if="activeEntity">

            <q-scroll-area
              :style="{height: height}"
            >
              <div class="">

                <div class="s-font-lg q-mb-xs text-weight-bold">{{activeEntity.name}}</div>

                <q-spinner
                  :thickness="2"
                  color="primary"
                  size="3em"
                  v-if="activeIrkipediaLoading"
                />

                <div class="menu-links flex q-gutter-y-sm q-gutter-x-md q-pt-xs">

                  <a :href="activeIrkipedia.url" class="menu-links__item s-link-wrapper text-accent" target="_blank"
                     v-if="activeIrkipedia">
                    <q-icon name="fas fa-caret-right"/>
                    <span class="s-link">статья на irkipedia.ru</span>
                  </a>

                </div>

              </div>
            </q-scroll-area>

          </div>


        </div>

      </template>

    </template>
  </el-widget>

</template>

<script>
  import CQueryView from '@common/query/component/view/view'
  import CMap from "~module/app/component/map/map";
  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";

  export default {
    extends: CQueryView,
    props: {
      height: {default: '700px'},
    },
    components: {
      CMap
    },
    apollo: {
      posts: generateQueryInfo('posts', require('~module/entity/graphql/getPosts.gql'), {loadingKey: 'activePostsLoading'}, {varPath: 'cqueries.posts.vars'}),
    },
    data() {
      return {
        activeNid: null,
        activeNidLoad: null,
        activePostsLoading: 0,
        activeIrkipedia: null,
        activeIrkipediaLoading: false,
        queries: {
          posts: {
            result: null,
            state: {isLoading: false}
          },
        }
      }
    },
    watch: {
      activeNid(val) {
        setTimeout(() => {
          this.activeNidLoad = val
          this.loadIrkipedia()
        }, 300)
      }
    },
    computed: {
      activeEntity() {
        return this.query.result.nodes.find(item => item.nid === this.activeNid)
      },
      cqueries() {

        return {
          posts: {
            vars: {
              filter: {
                type: 'post',
                tax: [
                  {taxonomy: "category", id: this.activeNidLoad ? this.activeNidLoad : 1443, withChildren: true}
                ],
              },
              nav: {
                limit: 10,
              },
              imageSize: 't1.5'
            },
          },
        }
      }
    },
    methods: {
      onListClick(entity) {
        this.activeNid = entity.nid
      },

      async loadIrkipedia() {

        if (this.activeEntity) {

          this.activeIrkipediaLoading = true

          const ipdReqParams = {}

          if (this.activeEntity.meta.irkipedia_code) {
            ipdReqParams.find_code = this.activeEntity.meta.irkipedia_code
          } else {
            ipdReqParams.find_name = this.activeEntity.name
          }

          try {
            const {data} = await this.$axios.get('http://irkipedia.ru/ext-term-teaser', {
              params: ipdReqParams
            })
            this.activeIrkipedia = data
          } catch (e) {
            this.activeIrkipedia = null
          }

          this.activeIrkipediaLoading = false

        } else {
          this.activeIrkipedia = null
          this.activeIrkipediaLoading = false
        }

      }
    },
  }
</script>

<style lang="scss" scoped>


  .places-list__item {

    padding: 6px 0 6px 0;

    &.active {
      background-color: $primary;
      padding: 3px 6px;

      a {
        color: #fff !important;
      }
    }
  }

  .menu-links {

  }

  .menu-links__item {
    display: inline-block;
  }

</style>
