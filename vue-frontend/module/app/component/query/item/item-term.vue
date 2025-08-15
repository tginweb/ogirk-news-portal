<template>
  <div class="query-item">
    <div class="i-content">
      <div class="i-row row q-col-gutter-sm ">

        <div class="col-24 col-lg-7">

          <component class="i-title s-font-lg" v-bind="bindLink">
            {{itemTitle}}
          </component>

        </div>

        <div class="col-24 col-lg-17">

          <div class="i-posts">

            <div
              class="i-post q-mb-md flex no-wrap"
              v-for="post of item.posts.nodes"
            >

              <template v-if="post.url">

                <div class="i-post-date q-mr-md">
                  {{$util.date.timestampToFormat(post.created, 'date')}}
                </div>

                <div>
                  <router-link
                    :to="post.url"
                    class="i-post-title"
                  >
                    {{post.title}}
                  </router-link>
                </div>

              </template>

            </div>

          </div>

        </div>

      </div>
    </div>
  </div>
</template>

<script>
  import CItem from './_item-term'

  export default {
    name: 'item-term',
    extends: CItem,
    data() {
      return {}
    },
    methods: {},
    computed: {
      host() {
        return this.item.post
      },

      routeHost() {
        return this.item
      },
    }
  }
</script>

<style lang="scss" scoped>

  .i-title {
    text-decoration: underline;
    line-height: 1.35;
    display: block;
    margin-top: -2px;
  }

  .i-post {
    a {
      text-decoration: underline;
    }
  }


</style>
