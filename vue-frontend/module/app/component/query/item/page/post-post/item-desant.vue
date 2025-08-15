<template>
  <div class="item-page-post-post item-page-post-post-2col">

    <div class="">
      <a href="/tag/severnyj-desant/" style="display: block;font-size: 0;">
        <img src="/statics/des-banner-new.gif" style="width: 100%;" class="gt-sm"/>
        <img src="/statics/des-banner-new.gif" style="width: 100%;" class="lt-md"/>
      </a>
    </div>


    <div style="display: flex;">

      <div style="flex-grow: 1;" class="gt-md">

        <a href="/tag/severnyj-desant/" style="display:block;width: 100%;height: 700px; background-image: url('/statics/des-left-new.gif'); background-position: center top; background-size: 100%; background-repeat: no-repeat; position: sticky;top:40px;"></a>

      </div>

      <div class="container q-px-lg" style="margin:0;max-width: 1000px;">

        <div class="q-mt-md">

            <CMetaMobile
                :fotoAuthor="fotoAuthor"
                :item="item"
                :share="shareData"
                :termsCategory="termsCategory"
                :termsTags="termsTags"
                :textAuthor="textAuthor"
            />

            <div class="c-header q-pb-lg q-mb-lg">

              <h1 class="c-header__title s-font-xxl s-font-md-4xl q-ma-none">
                {{itemTitle}}
              </h1>

              <h2 class="c-header__subtitle s-font-xxl q-ma-none q-mt-sm" style="line-height: 1.4em;" v-if="itemSubtitle">
                {{itemSubtitle}}
              </h2>

            </div>

            <div class="row q-col-gutter-xl">

              <div class="col-24">

                <div class="c-body" style="min-height: 100%;">

                  <q-resize-observer @resize="onResizeBody"/>

                  <div class="c-body__main" ref="bodyMain">

                    <CMediaHeader
                        :item="item"
                        class="c-media q-mb-lg"
                        v-if="item.meta.sm_media_view!=='none'"
                    />

                    <div
                        class="c-body__content sm-content-style"
                        v-html="contentFormatted"
                        ref="bodyContent"
                    />

                    <div v-if="item.meta.er_token" class="q-mt-md q-mb-md">
                      erid: {{item.meta.er_token}}
                    </div>



                  </div>


                </div>

              </div>

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
import CItem from './../../_item-post'
import CSidebar from '~module/app/layout/main/sidebar/article'
import CMediaHeader from './hero/hero-media'
import CMetaDesktop from './meta/meta-desktop'
import CMetaMobile from './meta/meta-mobile'
import CSeo from '~module/seo/component/links-bottom-article'
import MContentHandlerable from "@common/core/mixin/content-handlerable";

import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";
import {dom} from 'quasar'

const {height, width} = dom

export default {
  components: {
    CSidebar,
    CMediaHeader,
    CMetaDesktop,
    CMetaMobile,
    CSeo
  },
  extends: CItem,
  mixins: [MContentHandlerable],
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
    termsIsPolis() {
      return !!this.terms.find(item => [8041, 8092].indexOf(parseInt(item.nid)) > -1)
    },

    termsTagsById() {
      return  this.termsTags.reduce((map, item) => (map[item.nid] = item, map), {});
    },

    contentFormatted() {

      let content = this.item.contentFormatted

      if (content.replaceAll && false) {
        this.termsTags.forEach((tag) => {

          let count = 0
          const replace = tag.name;
          const re = new RegExp(replace + '(\w{0,2})',"ig");

          content = content.replaceAll(re, (match, r, t)=>{
            count++

            if (count > 3) {
              return match
            } else {
              console.log('rrr')

              console.log(r)
              console.log(t)

              return '<a href="'+tag.url+'">'+match+'</a>'
            }
          })

        })

      }

      return content
    },

    relatedPostsFilter() {

      const res = {
        type: 'post',
      }

      if (this.item.hub && this.termHub) {

        const hubTerms = {
          'sm-hub-term': [this.termHub.nid]
        }

        if (parseInt(this.item.hub.meta.sm_hub_related_terms_str))
          hubTerms['post_tag'] = [parseInt(this.item.hub.meta.sm_hub_related_terms_str)]

        res.terms = hubTerms

      } else {
        res.tax = [{taxonomy: "post_tag", op: 'in', ids: this.termsTags.map(term => term.nid)}]
      }

      return res
    },

    cqueries() {
      return {
        relatedPosts: {
          vars: {
            filter: this.relatedPostsFilter,
            nav: {
              limit: 6
            },
            imageSize: 't1.5'
          },
        },
      }
    },

    bodyBottomHeight() {
      return this.bodyFreeHeight - 200
    },

    otherPostsCount() {
      return Math.round(this.bodyBottomHeight / 124)
    }
  },
  methods: {
    onResizeBody(size) {
      this.bodyFreeHeight = size.height - height(this.$refs.bodyMain)
    }
  },
  beforeDestroy() {
    this.detachContentHandlers()
  },
  mounted() {

    this.attachContentHandlers(this.$refs['bodyContent'], Object.values(this.$registry.applyHooks('content/handlers')))

    setTimeout(() => {
      this.queries.relatedPosts.state.skip = false
    }, 10)
  },
  watch: {
    'screen.gt.md'(v) {

    }
  }
}
</script>

<style lang="scss" scoped>

.c-body__related {
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

</style>
