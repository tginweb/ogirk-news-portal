<template>
  <q-page class="q-mt-md q-mt-md-lg q-mb-lg">

    <component
        v-for="(item, index) of feed"
        :key="item._id"
        v-intersection="onItemIntersection"
        :data-id="index"
        :infeed="feed"
        :item="item"
        class="q-mb-xl"
        v-bind="bind(item)"
    />

  </q-page>
</template>

<script>
import CView_2col from '~module/app/component/query/item/page/post-post/item-2col'
import CView_3col from '~module/app/component/query/item/page/post-post/item-3col'
import CView_online from '~module/app/component/query/item/page/post-post/item-online'
import CView_desant from '~module/app/component/query/item/page/post-post/item-desant'
import CPage from "~module/app/component/route";

import setPageRouteData from "@common/core/lib/route/set-page-data";

const loadPageData = async (pageData, ctx) => {

  console.log('loadPageData')
  try {

    let {data} = await ctx.apolloClient.query({
      query: require('~module/entity/graphql/getPostPost.gql'),
      fetchPolicy: 'no-cache',
      variables: {
        filter: {
          slug: ctx.route.params.slug,
          type: 'post',
        },
        hub: true,
        content: true,
        gallery: true,
        authors: true,
        termsTaxonomy: ['category', 'post_tag', 'sm-hub-term', 'sm-author', 'sm-role'],
        view: 'public_full'
      },
    })


    pageData.entity = data.res

    if (pageData.entity) {
      if (pageData.entity.nid === 454803) {
        window.location.replace('http://irkobl.tilda.ws/100tovarov2022')
      }
    }

  } catch (e) {

  }

  return pageData;
}

const feedDisableFormats = [
  'chat'
];

export default {
  name: `Article`,
  extends: CPage,
  components: {
    CView_2col,
    CView_3col,
    CView_desant,
    CView_online
  },
  props: {},
  data() {
    return {
      feed: [],
      loading: false
    }
  },
  async preFetch(ctx) {
    return setPageRouteData('static', ctx, loadPageData)
  },
  computed: {
    firstPost() {
      return this.feed[0]
    },
    lastPost() {
      return this.feed[this.feed.length - 1]
    },
    postsNids() {
      return this.feed.map(post => post.nid)
    },
    feedEnabled() {
      return this.firstPost && feedDisableFormats.indexOf(this.firstPost.format) === -1
    },

    pageTitle() {


      if (this.pageData) {

        if (this.pageData.title) {
          return this.pageData.title
        } else if (this.pageData.entity) {
          return this.pageData.entity.name || this.pageData.entity.title
        }
      }
      return this.page.title
    },

    ogPageDesc() {
      if (this.pageData) {

        if (this.pageData.entity) {
          if (this.pageData.entity.meta.description) {
            return this.pageData.entity.meta.description
          }
          return this.pageData.entity.share && this.pageData.entity.share.description || this.pageData.entity.excerpt
        }
      }
      return this.page.description
    }
  },
  methods: {

    entityTermsByTax(entity) {
      return entity && entity.terms && entity.terms.reduce((map, item) => {
        if (!map[item.taxonomy]) map[item.taxonomy] = {}
        map[item.taxonomy][item.slug] = item
        return map
      }, {}) || {}
    },

    bind(entity) {

      const res = {}

      const termsByTax = this.entityTermsByTax(entity)

      let roles

      if (roles = termsByTax['sm-role']) {
        if (roles['card']) {
          res.is = 'CView_cards'
        }
      }

      if (termsByTax['post_tag'] && termsByTax['post_tag']['severnyj-desant']) res.is = 'CView_desant'


      if (entity.format === 'chat') {
        res.is = 'CView_online'
      }

      res.is = res.is || 'CView_3col'

      return res
    },

    async onIntersection(info) {

      if (info.isIntersecting) {
        if (this.feedEnabled) {
          await this.loadNext();
        }
      }
    },

    async onItemIntersection(info) {

      if (info.isIntersecting) {
        const item = this.feed[parseInt(info.target.dataset.id)]

        if (item.nid !== this.pageData.entity.nid) {
          history.pushState({}, '', item.url)
        }
      }

    },

    async loadNext() {

      let {data} = await this.$apollo.query({
        query: require('~module/entity/graphql/getPosts.gql'),
        fetchPolicy: 'no-cache',
        variables: {
          filter: {
            type: 'post',
            excludeNids: this.postsNids,
            excludeFormat: feedDisableFormats
          },
          nav: {
            limit: 1
          },
          hub: true,
          authors: true,
          content: true,
          gallery: true,
          imageSize: 'd1.6',
          termsTaxonomy: ['category', 'post_tag', "sm-hub-term"],
          view: 'public_full'
        },
      })

      if (data.res.nodes.length) {
        this.addEntity(data.res.nodes[0])
      }
    },

    async addEntity(entity) {
      this.feed.push(entity)

      let {data} = await this.$apollo.mutate({
        mutation: require('~module/entity/graphql/entityStat_HitView.gql'),
        variables: {
          entityType: 'post',
          entityNid: entity.nid,
        },
      })
    }
  },
  watch: {
    'pageData.entity': {
      immediate: true,
      handler(newVal, oldVal) {
        if (this.pageData.entity) {
          this.feed = []
          this.addEntity(this.pageData.entity)
        }
      }
    }
  }
}
</script>
