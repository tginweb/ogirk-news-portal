<template>
  <q-page class="q-mt-lg">

    <CLayout>
      <template v-slot:header>
        <el-page-header title="Видео"/>

      </template>

      <template v-slot:default>
        <query-items-grid
          :item="{
            class: 'col-24',
            is: 'query-item-special',
            elements: {
              title: {
                class: 's-font-md'
              }
            }
          }"
          :items="queries.postsArchive.result.nodes"
          rowClass="q-col-gutter-xl"
          v-if="queries.postsArchive.result"
        />
      </template>
    </CLayout>


  </q-page>
</template>

<script>
  import CLayout from '~module/app/layout/main/page/1cols'
  import CQueryViewVideoPlayer from "~module/app/component/query/view/view-video-player";

  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";

  export default {
    components: {
      CLayout,
      CQueryViewVideoPlayer
    },
    apollo: {

      video: generateQueryInfo('video', require('~module/entity/graphql/getPosts.gql')),
      postsArchive: generateQueryInfo('postsArchive', require('~module/entity/graphql/getPosts.gql')),

    },
    data() {
      return {
        queries: {
          video: {
            vars: {
              filter: {
                type: 'post',
                format: 'video',
              },
              nav: {
                limit: 10
              },
              imageSize: 'm1.78',
              content: true,
              view: 'public_full',
            },
            state: {
              isLoading: false,
              mode: null
            },
            result: null,
          },
          postsArchive: {
            vars: {
              filter: {
                type: 'post',
                format: 'video',
              },
              nav: {
                limit: 20
              },
              imageSize: 't1.4'
            },
            state: {
              isLoading: false
            },
            result: null
          },
        },
        calendarDate: null
      }
    },
    computed: {},

  }
</script>
