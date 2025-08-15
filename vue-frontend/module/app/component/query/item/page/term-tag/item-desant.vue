<template>
  <div>
    <div class="">
      <a href="/tag/severnyj-desant/" style="display: block;font-size: 0;">
        <img src="/statics/des-banner-new.gif" style="width: 100%;" class="gt-sm"/>
        <img src="/statics/des-banner-new.gif" style="width: 100%;" class="lt-md"/>
      </a>
    </div>

    <div style="display: flex;" >

      <div style="flex-grow: 1;" class="gt-md">

        <a href="/tag/severnyj-desant/" style="display:block;width: 100%;height: 700px; background-image: url('/statics/des-left-new.gif'); background-position: center top; background-size: 100%; background-repeat: no-repeat; position: sticky;top:40px;"></a>

      </div>

      <div class="container q-px-lg" style="margin:0;max-width: 1000px;">

        <el-page-header
            :title="item.name"
            class="q-mb-md q-mt-md"
        >
        </el-page-header>

        <div class="i-content">

          <div class="i-feed">

            <query-view
                :pager="{desktopType: 'prevnext', mobileType: 'loadmore', align: 'center'}"
                :queryHandler="()=>$apollo.queries.posts"
                :queryResult.sync="queries.posts.result"
                :queryState.sync="queries.posts.state"
            >
              <template v-slot:default="{items, state, loading, onNavMore}">

                <q-timeline
                    class="q-my-none"
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


      </div>

      <div style="flex-grow: 1;" class="gt-md">

        <a href="/tag/severnyj-desant/"  style="display:block;width: 100%;height: 700px; background-image: url('/statics/des-right-new-new.gif'); background-position: center top; background-size: 100%; background-repeat: no-repeat; position: sticky;top:40px;"></a>

      </div>

    </div>


  </div>
</template>

<script>
  import CItem from './../../_item-term'
  import CDosie from './parts/dosie'

  import CPageLayout from '~module/app/layout/main/page/1cols'
  import CPageSidebar from '~module/app/layout/main/sidebar/common'
  import MRoutable from '~module/app/mixin/routable'
  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";
  import formatDefault from '~module/app/lib/util/date/format-default'

  export default {
    extends: CItem,
    mixins: [MRoutable],
    components: {
      CPageLayout,
      CPageSidebar,
      CDosie
    },
    props: {
      irkipedia: {}
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
        return !!this.irkipedia || this.item.nid === 7895 || this.item.nid === 7951;
      },

      cqueries() {
        return {
          posts: {
            vars: {
              filter: {
                type: 'post',
                tax: [{taxonomy: "post_tag", id: this.item.nid}],
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
    height: 400px;

    a {
      color: #fff;
    }
  }

  .i-title {
    font-size: 48px;
    font-weight: bold;
    text-align: center;
  }

  .i-subtitle {
    font-size: 34px;
    font-weight1: bold;
    text-align: center;
  }

  .i-about {
    position: relative;
    background: #FFF;
    max-width: 900px;
    border-radius: 5px;
  }

</style>
