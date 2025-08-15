<template>

  <div v-if="query.result">

    <div class="">

      <CMap
        :entities="query.result.nodes"
        :height="height"
        :mapTypeControl="false"
        :zoom="4"
        mapType="roadmap"
        :value="activeNid"
        :locked="true"
      />

    </div>

    <div class="q-mt-lg" v-if="activeEntity">

      <div class="s-font-lg q-mb-xs text-weight-bold">{{activeEntity.name}}</div>

      <q-spinner
        :thickness="2"
        color="primary"
        size="3em"
        v-if="activeIrkipediaLoading"
      />
      <template v-else>

        <div v-if="activeIrkipedia">

          <div
            v-html="activeIrkipedia.teaser.substring(0, 600) + '...'"
          />

        </div>

      </template>

      <div class="menu-links  q-gutter-y-md q-pt-lg">

        <a :href="activeIrkipedia.url" class="menu-links__item s-link-wrapper text-accent block" target="_blank"
           v-if="activeIrkipedia">
          <q-icon name="fas fa-caret-right"/>
          <span class="s-link">статья на irkipedia.ru</span>
        </a>

        <a class="menu-links__item s-link-wrapper text-accent block" href="ssss" target="_blank">
          <q-icon name="fas fa-caret-right"/>
          <span class="s-link">на проекте "Иркутский район"</span>
        </a>

      </div>

    </div>

  </div>


</template>

<script>
  import CQueryView from '@common/query/component/view/view'
  import CMap from "~module/app/component/map/map";
  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";

  export default {
    extends: CQueryView,
    props: {
      height: {default: '700px'},
      value: {}
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
    mounted() {

    },
    watch: {
      activeNid(val) {
        setTimeout(() => {
          this.activeNidLoad = val
          this.loadIrkipedia()
        }, 300)
      },

      'query.result'() {

        setTimeout(()=>{
          this.setActiveNid(this.value)
        }, 100)

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
                limit: 7,
              },
              imageSize: 't1.5'
            },
          },
        }
      }
    },
    methods: {

      setActiveNid(nid) {
        this.activeNid = nid
      },

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
