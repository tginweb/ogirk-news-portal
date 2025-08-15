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

      <div class="row q-col-gutter-md" style="max-width: 500px;">
        <div class="col-10 col-md-10">
          <div class="i-thumb">
            <img
                v-lazy="itemImageThumbSrc.split('https://www.ogirk.ru')[1]"
                class="i-thumb__image"
            />
          </div>
        </div>
        <div class="col-14 col-md-14">
          <div class="q-gutter-y-md">

            <div class="s-font-md s-font-md-lg">
              <span class="text-grey-7">Дата:</span>
              <span class="text-grey-10"> {{ dateFormatted }}</span>
            </div>
            <div class="s-font-md s-font-md-lg text-grey-7">
              <span class="text-grey-7">Номер:</span>
              <span class="text-grey-10"> {{ item.meta.sm_post_num_year }} / {{ item.meta.sm_post_num_all }}</span>
            </div>

            <div
                v-for="(doc, index) of docs"
                :key="doc.src"
            >
              <q-btn
                  color="primary"
                  outline
                  label="скачать PDF номера"
                  class=""
                  :href="doc.src"
                  target="_blank"
                  :icon="$icons.fasFileDownload"
              >
              </q-btn>
            </div>

          </div>
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
    itemTitle() {
      return this.termIssue.name + ' от ' + this.dateFormatted
    },
    dateFormatted() {
      return this.$util.date.timestampToFormat(this.itemDate, 'DD MMMM YYYY')
    },
    termIssue() {
      return this.termsByTax['sm-other-issue'] && this.termsByTax['sm-other-issue'].length && this.termsByTax['sm-other-issue'][0];
    },
    calendarFilter() {
      return {
        type: 'sm-other-issue-print',
        tax: [{taxonomy: "sm-issue", id: this.termIssue.nid}]
      }
    },

    bindCalendar() {
      return {
        date: null,
        filter: {
          type: 'sm-other-issue-print',
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


.i-thumb {
  border: 1px solid #ddd;
  padding: 5px;
}

</style>
