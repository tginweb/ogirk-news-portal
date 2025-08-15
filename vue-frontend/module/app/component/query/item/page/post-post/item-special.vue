<template>
  <div class="item-page-post-post item-page-post-post-2col">

    <div class="container">

      <div class="row q-col-gutter-x-lg q-col-gutter-y-md">

        <div class="col-24" id="page-left-col">

          <CMetaMobile
            :item="item"
            :share="shareData"
            :termsCategory="termsCategory"
            :termsTags="termsTags"
          ></CMetaMobile>

        </div>

        <div class="col-24">

          <div class="c-header q-pb-lg q-mb-lg">

            <h1 class="c-header__title s-font-xxl s-font-md-4xl q-ma-none">
              {{itemTitle}}
            </h1>

          </div>

          <CMediaHeader
            :item="item"
            class="c-media q-mb-lg"
          />

          <div class="c-body" style="min-height: 100%;">

            <div class="c-body__main" ref="bodyMain">

              <div class="c-body__content sm-content-style" v-html="item.contentFormatted"></div>

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
  import CMetaDesktop from './meta/meta-desktop'
  import CMetaMobile from './meta/meta-mobile'

  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";
  import {dom} from 'quasar'

  const {height, width} = dom

  export default {
    components: {
      CSidebar,
      CMediaHeader,
      CMetaDesktop,
      CMetaMobile,
    },
    extends: CItem,
    props: {},
    apollo: {
      otherPosts: generateQueryInfo('otherPosts', require('~module/entity/graphql/getPosts.gql'), {}),
      relatedPosts: generateQueryInfo('relatedPosts', require('~module/entity/graphql/getPosts.gql'), {}, {
        varPath: 'cqueries.relatedPosts.vars',
      }),
    },

    data() {
      return {
        queries: {
          relatedPosts: {
            result: null,
            state: {skip: true}
          },
          otherPosts: {
            vars: {
              filter: {
                type: 'post',
              },
              nav: {
                limit: 10
              },
              imageSize: 't1.5'
            },
            result: null,
            state: {}
          },
        },
        bodyHeight: null,
        bodyMainHeight: null,
        bodyFreeHeight: 0
      }
    },
    computed: {

      bodyBottomHeight() {
        return this.bodyFreeHeight - 145
      },

      otherPostsCount() {
        return Math.round(this.bodyBottomHeight / 142)
      }
    },
    methods: {
      onResizeBody(size) {
        this.bodyFreeHeight = size.height - height(this.$refs.bodyMain)
      }
    },
    mounted() {

      setTimeout(() => {
        this.queries.relatedPosts.state.skip = false
      }, 10)
    }
  }
</script>

<style lang="scss" scoped>


  .c-related {
    border-radius: 2px;
    box-shadow: 0px 1px 15px 0px rgba(0, 0, 0, .1);

    /deep/ .w-header {
      padding-left: 15px;
      padding-right: 15px;
    }

    /deep/ .w-body {
      padding-left: 15px;
      padding-right: 15px;
    }
  }

  .c-body__content {
    font-size: 22px !important;
  }

</style>
