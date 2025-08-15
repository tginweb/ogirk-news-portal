<template>
  <q-page class="q-mt-md q-mt-md-lg q-mb-lg">

    <CItem
      :item="item"
      :key="item._id"
      v-for="item of feed"
      class="q-mb-xl"
      :infeed="feed"
    ></CItem>


  </q-page>
</template>

<script>
  import CItem from '~module/app/component/query/item/page/post-post/item-special'

  import setPageRouteData from '@common/core/lib/route/set-page-data'

  const loadPageData = async (pageData, ctx) => {

    try {

      let {data} = await ctx.apolloClient.query({
        query: require('~module/entity/graphql/getPost.gql'),
        fetchPolicy: 'no-cache',
        variables: {
          filter: {
            slug: ctx.route.params.slug,
          },
          hub: true,
          content: true,
          gallery: true,
          termsTaxonomy: ['category', 'post_tag', 'sm-hub-term', 'sm-author'],
          view: 'public_full'
        },
      })

      pageData.entity = data.res

    } catch (e) {

      console.log(e)
    }

    return pageData;
  }

  export default {
    name: `Article`,
    components: {
      CItem,
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
      lastPost() {
        return this.feed[this.feed.length-1]
      },
      postsNids() {
        return this.feed.map(post => post.nid)
      }
    },
    methods: {

      async onIntersection(entry) {

        if (entry.isIntersecting) {

          let {data} = await this.$apollo.query({
            query: require('~module/entity/graphql/getPosts.gql'),
            fetchPolicy: 'no-cache',
            variables: {
              filter: {
                type: 'post',
                excludeNids: this.postsNids
              },
              nav: {
                limit: 1
              },
              hub: true,
              content: true,
              gallery: true,
              termsTaxonomy: ['category', 'post_tag', "sm-hub-term"],
              view: 'public_full'
            },
          })

          //console.log(data)

          this.feed.push(data.res.nodes[0])
        }
      }
    },
    created() {

      //  console.log(this.$apolloProvider)
    },

    mounted() {

    },

    watch: {

      'pageData.entity': {
        immediate: true,
        handler(newVal, oldVal) {
          if (this.pageData.entity)
            this.feed = [this.pageData.entity]
        },
      },

    },

  }
</script>
