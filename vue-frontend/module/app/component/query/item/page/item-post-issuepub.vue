<template>
  <CLayout>
    <template v-slot:header>

      <el-page-header :title="itemTitle">

        <template v-slot:side>

          <CCalendarDropdown v-bind="bindCalendar"/>

        </template>

      </el-page-header>

    </template>
    <template v-slot:default>

      <div class="i-docs flex q-gutter-x-md q-mb-lg">
        <div
          v-for="(doc, index) of docs"
          class="q-mb-sm"
        >
          {{doc.label}}
          <a
            class="s-link-wrapper q-ml-sm text-accent"
            :href="doc.src"
            target="_blank"
          >
            <q-icon name="fas fa-download" class="s-link-icon q-mr-sm"/>
            <span class="s-link">скачать</span>
          </a>
        </div>
      </div>

      <query-items-grid
        :item="{
          is: 'query-item-1',
          class: 'col-24 col-md-12',
          expandable: false,
          elements: {
            title: {
              defclass: 's-font-xxl'
            },
            excerpt: {length: 200}
          }
        }"
        :items="queries.top.result.nodes"
        class="q-mb-lg"
        rowClass="q-col-gutter-lg"
        v-if="queries.top.result"
      />

      <query-items-grid
        :item="{
          class: 'col-24 col-md-12 q-mt-lg',
          is: 'query-item-5',
          elements: {
            content: {
              class: ''
            },
            row: {
              class: 'q-col-gutter-md'
            }
          }
        }"
        :items="queries.main.result.nodes"
        rowClass="q-col-gutter-lg"
        v-if="queries.main.result"
      />

    </template>
  </CLayout>

</template>

<script>
  import CItem from './../_item-post'
  import CLayout from '~module/app/layout/main/page/1cols'
  import CCalendarDropdown from '~module/app/component/query/filter/calendar/dropdown'

  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";

  export default {
    extends: CItem,
    components: {
      CLayout,
      CCalendarDropdown
    },
    apollo: {
      top: generateQueryInfo('top', require('~module/entity/graphql/getPosts.gql'), {}),
      main: generateQueryInfo('main', require('~module/entity/graphql/getPosts.gql'), {}),
    },
    data() {
      return {
        queries: {
          top: {
            vars: {
              filter: {
                type: 'post',
                issue_print: this.item.nid,
              },
              nav: {
                limit: 2
              },
              imageSize: 't1.5',
              cache: 'TEMP_LG',
              excluder: true
            },
            result: null
          },
          main: {
            vars: {
              filter: {
                type: 'post',
                issue_print: this.item.nid,
              },
              nav: {
                limit: 100
              },
              imageSize: 't1.5',
              cache: 'TEMP_LG',
              await: 'top',
              excluder: true,
            },
            result: null
          },
        },
      }
    },
    computed: {
      calendarFilter() {
        return {
          type: 'sm-issue-print',
          tax: [{taxonomy: "sm-issue", id: this.termIssue.nid}]
        }
      },

      bindCalendar() {
        return {
          date: null,
          filter: {
            type: 'sm-issue-print',
            tax: [{taxonomy: "sm-issue", id: this.termIssue.nid}]
          },
          url: '/issue/' + this.termIssue.slug + '/$date'
        }
      },

      docs() {
        const res = []

        if (this.item.meta.sm_file_contents)
          res.push({
            label: 'Файл статей номера',
            src: 'https://www.ogirk.ru' + this.item.meta.sm_file_contents
          })

        if (this.item.meta.sm_file_official)
          res.push({
            label: 'Файл правовых актов номера',
            src: 'https://www.ogirk.ru' + this.item.meta.sm_file_official
          })

        return res
      }
    },
  }
</script>

<style lang="scss" scoped>


</style>
