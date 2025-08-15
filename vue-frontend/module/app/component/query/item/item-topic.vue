<template>
  <div class="query-item" v-bind="bind">
    <div
      class="i-wrap"
      v-lazy:background-image="$image.resolveUrl(item.meta.sm_hub_image, 2000)"
    >

      <div class="i-overlay -mode-fh"></div>

      <div class="container">

        <div class="i-content">

          <div class="row q-col-gutter-lg items-center">

            <div class="col-24 col-md-12">

              <div class="i-info q-py-md">

                <router-link :to="compUrl">
                  <h3 class="i-title s-font-3xl s-font-sm-4xl s-font-md-5xl q-ma-none" :class="elementParam('title', 'class')">
                    {{itemTitle}}
                  </h3>
                </router-link>

                <q-btn class="q-mt-lg" color="white" outline size="lg" :to="compUrl" v-if="screen.gt.md">
                  Подробнее
                </q-btn>

              </div>

            </div>

            <div class="col-24 col-md-12">


              <q-timeline class="q-my-none q-pt-md q-pt-md-xl q-pb-md-md" color="white" dark v-if="queries.news.result">

                <q-timeline-entry
                  v-for="item of queries.news.result.nodes"
                  class="i-post"
                  :subtitle="$util.date.timestampToFormat(item.created, 'DD MMMM YYYY')"
                  :title="item.title"
                  @click="$router.push(item.url)"
                  :key="item._id"
                >
                </q-timeline-entry>

              </q-timeline>

              <q-btn class="q-mb-md full-width" color="white" outline size="lg" :to="compUrl" v-if="screen.lt.md">
                весь сюжет
              </q-btn>

            </div>

          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import CItem from './_item-post'
  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";

  export default {
    name: 'item-topic',
    extends: CItem,
    apollo: {
      news: generateQueryInfo('news', require('~module/entity/graphql/getPosts.gql'), {},{varPath: 'cqueries.news.vars'}),
    },
    data() {
      return {
        queries: {
          news: {
            result: null
          },
        }
      }
    },
    computed: {
      cqueries() {
        return {
          news: {
            vars: {
              filter: {
                type: 'post',
                terms: this.hubRelatedTerms
              },
              nav: {
                limit: 3
              },
              imageSize: 't1.5',
              cache: 'TEMP_LG'
            },
          },
        }
      },

    }
  }
</script>

<style lang="scss" scoped>

  .i-wrap {
    position: relative;
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center center;
    color: #fff;

    a {
      color: #fff;
    }
  }

  .i-content {
    height: 100%;
    overflow: hidden;
  }

  .i-media {
    position: relative;
  }

  .i-info {
    text-shadow: 0 0 2px rgba(0, 0, 0, 0.5);
    position: relative;
  }

  .i-title {
    font-weight: 800;
    line-height: 1.4em;
  }

  .i-post {
    cursor: pointer;
    /deep/ {
      .q-timeline__title {
        font-weight: 600;
        text-shadow: 0 0 2px rgba(0, 0, 0, 0.5);
      }
      .q-timeline__content {

      }
    }
  }


</style>
