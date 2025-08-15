<template>
  <div>

    <div
      :style="{
        'background-image': 'url(https://www.ogirk.ru'+ (item.meta.sm_hub_image || item.image && item.image.src) +')',
      }"
      class="i-header"

    >
      <div class="i-overlay -mode-fh" style="background-color: rgba(0, 0, 0, 0.1)"/>

      <div class="i-header__inner container flex">

        <div class="full-width q-mt-auto q-mb-auto text-center">

          <div class="i-title q-px-md q-py-md">
            {{itemTitle}}
          </div>

          <div class="i-subtitle q-mt-lg" v-if="subtitle">
            {{subtitle}}
          </div>

        </div>

      </div>

    </div>

    <div class="container">

      <div class="i-content">

        <div class="row q-col-gutter-xl">

          <div class="col-24 col-md-15">

            <div class="i-feed">

              <query-view
                :pager="{desktopType: 'prevnext', mobileType: 'loadmore', align: 'center'}"
                :queryHandler="()=>$apollo.queries.posts"
                :queryResult.sync="queries.posts.result"
                :queryState.sync="queries.posts.state"
              >
                <template v-slot:default="{items, state, loading, onNavMore}">

                  <q-timeline
                    class="q-mt-xl"
                    v-if="items"
                  >

                    <q-timeline-entry
                      :key="item._id"
                      :subtitle="datetimeFormatDefault(item.created)"
                      class="i-post"
                      v-for="item of items"
                    >
                      <query-item-6
                        :item="item"
                        class=""
                      />
                    </q-timeline-entry>

                  </q-timeline>

                </template>

              </query-view>

            </div>

          </div>

          <div class="col-24 col-md-9 " id="col-right">

            <div class="q-pt-md" v-hc-sticky="{stickTo: '#col-right', top: 30}" v-if="hasAbout">

              <div class="i-about q-mt-xl q-px-xl q-py-xl shadow-2 sm-content-style">

                <div v-html="item.contentFormatted"/>

              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

  </div>
</template>

<script>
  import CItem from './../_item-post'
  import CPageLayout from '~module/app/layout/main/page/1cols'
  import CPageSidebar from '~module/app/layout/main/sidebar/common'
  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";
  import formatDefault from '~module/app/lib/util/date/format-default'
  import MRoutable from "~module/app/mixin/routable";

  export default {
    extends: CItem,
    mixins: [MRoutable],
    components: {
      CPageLayout,
      CPageSidebar
    },
    apollo: {
      posts: generateQueryInfo('posts', require('~module/entity/graphql/getPosts.gql'), {}, {varPath: 'cqueries.posts.vars'}),
    },
    data() {
      return {
        queries: {
          posts: {
            result: null,
            state: {isLoading: false},
          },
        },
        calendarDate: null,
        datetimeFormatDefault: formatDefault,
      }
    },
    computed: {

      hasAbout() {
        return !!this.item.contentFormatted;
      },

      subtitle() {
        return this.item.meta.sm_hub_banner_subtitle
      },

      cqueries() {
        return {
          posts: {
            vars: {
              filter: {
                type: 'post',
                terms: this.hubRelatedTerms,
                ...this.routeFilter,
              },
              nav: {
                limit: 30,
                ...this.routeNav,
              },
              imageSize: 't2.4',
              imageHook: false,
              cache: 'TEMP_LG'
            },
            result: null
          },
        }
      }
    },
  }
</script>


<style lang="scss" scoped>

  .i-header {
    position: relative;
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center center;
    color: #fff;
    height: 340px;

    a {
      color: #fff;
    }
  }

  .i-header__inner {
    position: relative;
    height: 100%;
  }

  .i-content {

  }

  .i-title {
    font-size: 48px;
    font-weight: bold;
    text-align: center;
    background-color: rgba(0, 0, 0, 0.5);
    display: inline-block;
  }

  .i-subtitle {
    font-size: 34px;
    font-weight1: bold;
    text-align: center;
    display: inline-block;
  }

  .i-about {
    position: relative;
    background: #FFF;
    display: flex;
    max-width: 900px;
    border-radius: 5px;
    font-size: 18px;
  }

  .i-feed {

  }

</style>
