<template>
  <div class="query-item" v-bind="bind">
    <div :class="elmContent.class" class="i-content">

      <div :class="elmRow.class" class="i-row row">

        <div class="col-5">
          <div class="i-media">
            <div class="i-thumb">
              <component v-bind="bindLink">
                <img
                  v-lazy="itemImageThumbSrc.split('https://www.ogirk.ru')[1]"
                  class="i-thumb__image"
                />
              </component>
            </div>
          </div>
        </div>

        <div class="col-19">
          <div class="i-info">

            <component v-bind="bindLink">

              <h3 class="i-title q-ma-none q-mb-md s-font-md s-font-sm-xl">
                {{termIssue.name}} {{itemDateFormatted}}
              </h3>
              <div class="q-mb-md text-grey-6">
                Номер: {{item.meta.sm_post_num_year}} / {{item.meta.sm_post_num_all}}
              </div>
            </component>


          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import CItem from './_item-post'

  export default {
    name: 'item-district-issuepub',
    props: {
      linkToIssue: {}
    },
    extends: CItem,
    data() {
      return {
        elementDefaults: {
          date: {
            format: 'DD MMMM YYYY'
          },
        }
      }
    },
    methods: {},
    computed: {
      compUrl() {
        return this.linkToIssue ? '/district-issue/' + this.termIssue.slug : '/district-issue-pub/' + this.item.nid
      },
      termIssue() {
        return this.termsByTax['sm-other-issue'] && this.termsByTax['sm-other-issue'].length && this.termsByTax['sm-other-issue'][0];
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
            label: 'Файл правовых актов',
            src: 'https://www.ogirk.ru' + this.item.meta.sm_file_official
          })

        return res
      }
    }
  }
</script>

<style lang="scss" scoped>

  .query-item {
  }

  .i-excerpt {
    font-size: 17px;
  }

  .i-info {
    width: 100%;
    padding: 0;
    bottom: 0;
    box-sizing: border-box;
  }

  .i-thumb {
    border: 1px solid #ddd;
    padding: 5px;
  }

  .i-title {
    font-weight: 700;
    line-height: 1.3em;
  }

  .i-meta-date {
    color: #777;
  }

</style>
