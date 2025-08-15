<template>
  <div>

    <div class="q-gutter-y-xl">


      <el-widget
        title="Важное"
      >
        <query-view
          :queryResult="queries.important.result"
          :queryState="queries.important.state"
        >
          <template v-slot:default="{items, loading}">
            <query-items-grid
              :item="{
                class: 'col-24',
                is: 'query-item-3',
                elements: {
                  row: {
                    class: 'q-col-gutter-md'
                  },
                  content: {
                    class: 'q-py-md s-border-bottom'
                  }
                }
              }"
              :items="items"
            />
          </template>
        </query-view>
      </el-widget>

      <ad-zone
        :ads="adsByZoneSlug[2]"
        :limit="2"
        class="q-pt-lg"
        zoneCode="2"
      >
        <template v-slot:default="{items}">
          <query-items-grid
            :item="{
                     class: 'col-24'
                    }"
            :items="items"
            class="q-gutter-lg"
            rowClass="q-col-gutter-y-lg"
          />
        </template>
      </ad-zone>


      <el-widget
        moreLabel="все"
        moreUrl="/foto"
        title="Фоторепортажи"
      >
        <query-items-grid
          :item="{
            class: 'col-24',
            is: 'query-item-1'
          }"
          :items="queries.fotoreports.result.nodes"
          v-if="queries.fotoreports.result"
        />
      </el-widget>

      <ad-zone
        :ads="adsByZoneSlug[4]"
        :limit="1"
        class="q-pt-lg"
        zoneCode="4"
      >
        <template v-slot:default="{items}">
          <query-items-grid
            :item="{
                     class: 'col-24'
                    }"
            :items="items"
            class="q-gutter-lg"
            rowClass="q-col-gutter-y-lg"
          />
        </template>
      </ad-zone>

      <el-widget
        title="Читаемое"
      >
        <query-view
          :queryResult="queries.popular.result"
          :queryState="queries.popular.state"
        >
          <template v-slot:default="{items, loading}">
            <query-items-grid
              :item="{
                class: 'col-24',
                is: 'query-item-index',
                elements: {
                }
              }"
              :items="items"
              rowClass="q-col-gutter-md"
            />
          </template>
        </query-view>
      </el-widget>

      <el-widget
        title="Проекты"
      >
        <query-items-grid
          :item="{
            class: 'col-24',
            is: 'query-item-hub-banner-col'
          }"
          :items="queries.hubsProjects.result.nodes"
          rowClass="q-col-gutter-md"
          v-if="queries.hubsProjects.result"
        />
      </el-widget>

    </div>

  </div>
</template>

<script>
  import generateQueryInfo from '@common/graphql-apollo/lib/generate-query-info'
  import MAdvertable from '~module/ad/mixin/advertable'

  export default {
    name: 'layout.sidebar.article',
    serverCacheKey1: props => 'layout.sidebar.article',
    components: {},
    mixins: [MAdvertable],
    props: {
      hideBanner: {}
    },
    apollo: {
      important: generateQueryInfo('important', require('~module/entity/graphql/getPosts.gql'), {}),
      quotes: generateQueryInfo('quotes', require('~module/entity/graphql/getPosts.gql'), {}),
      fotoreports: generateQueryInfo('fotoreports', require('~module/entity/graphql/getPosts.gql'), {}),
      hubsProjects: generateQueryInfo('hubsProjects', require('~module/entity/graphql/getPosts.gql'), {}),
      popular: generateQueryInfo('popular', require('~module/entity/graphql/getPosts.gql'), {}),
    },
    data() {
      return {
        queries: {

          hubsProjects: {
            vars: {
              filter: {
                type: 'sm-hub-post',
                tax: [{taxonomy: "sm-hub-type", slugs: ["projects", "landing"]}],
              },
              nav: {
                limit: 4
              },
              imageSize: 't1.5',
              cache: 'TEMP_MD'
            },
            result: null
          },
          important: {
            vars: {
              filter: {
                type: 'post',
                tax: [
                  {taxonomy: "sm-role", slug: "frontpage-top"}
                ],
              },
              nav: {
                limit: 4
              },
              imageSize: 't1.5',
              cache: 'TEMP_MD'
            },
            result: null
          },
          quotes: {
            vars: {
              filter: {
                type: 'post',
                tax: [
                  {taxonomy: "sm-role", slug: "quote"}
                ],
              },
              nav: {
                limit: 4
              },
              imageSize: 't1.5',
              cache: 'TEMP_MD'
            },
            result: null
          },
          fotoreports: {
            vars: {
              filter: {
                type: 'post',
                format: 'gallery'
              },
              nav: {
                limit: 1
              },
              imageSize: 'm2',
              cache: 'TEMP_MD'
            },
            result: null
          },
          popular: {
            vars: {
              filter: {
                type: 'post',
              },
              nav: {
                limit: 4
              },
              imageSize: 'm2',
              cache: 'TEMP_MD'
            },
            result: null
          },
        }
      }
    },
  }
</script>
