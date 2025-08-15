<template>
  <q-page class="q-mt-lg q-mb-xl">

    <CPageLayout>
      <template v-slot:default>

        <div class="q-mb-xl">

          <el-page-header title="Лендинги" class="q-mb-lg"/>

          <query-items-grid
            :items="queries.landings.result.nodes"
            :item="{
              class: 'col-24 col-md-12',
              is: screen.gt.sm ? 'query-item-hub-banner-row' : 'query-item-hub-banner-col',
              elements: {
                content: {
                  class: 'q-py-lg'
                },
                row: {
                  class: 'q-col-gutter-md'
                }
              }
            }"
            rowClass="q-col-gutter-lg"
            v-if="queries.landings.result"
          />
        </div>

        <div>

          <el-page-header title="Проекты" class="q-mb-lg"/>

          <query-items-grid
            :items="queries.projects.result.nodes"
            :item="{
              class: 'col-24 col-md-12',
              is: screen.gt.sm ? 'query-item-hub-banner-row' : 'query-item-hub-banner-col',
              elements: {
                content: {
                  class: 'q-py-lg'
                },
                row: {
                  class: 'q-col-gutter-md'
                }
              }
            }"
            rowClass="q-col-gutter-lg"
            v-if="queries.projects.result"
          />
        </div>

      </template>
    </CPageLayout>

  </q-page>
</template>

<script>
  import CPageLayout from '~module/app/layout/main/page/1cols'
  import CPageSidebar from '~module/app/layout/main/sidebar/common'
  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";
  import CPage from "~module/app/component/route";

  export default {
    name: 'page.projects',
    extends: CPage,
    components: {
      CPageLayout,
      CPageSidebar
    },
    apollo: {

      projects: generateQueryInfo('projects', require('~module/entity/graphql/getPosts.gql')),
      landings: generateQueryInfo('landings', require('~module/entity/graphql/getPosts.gql')),

    },
    data() {
      return {
        page: {
          title: 'Проекты'
        },
        queries: {
          projects: {
            vars: {
              filter: {
                type: 'sm-hub-post',
                tax: [{taxonomy: "sm-hub-type", slug: "projects"}],
              },
              nav: {
                limit: 30,
                sortField: "menuOrder",
                sortAscending: true
              },
              imageSize: 't1.5',
              cache: 'TEMP_LG'
            },
            state: {
              isLoading: false
            },
            result: null
          },
          landings: {
            vars: {
              filter: {
                type: 'sm-hub-post',
                tax: [{taxonomy: "sm-hub-type", slug: "landing"}],
              },
              nav: {
                limit: 30,
                sortField: "menuOrder",
                sortAscending: true
              },
              imageSize: 't1.5',
              cache: 'TEMP_LG'
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
    computed: {

    },

  }
</script>
