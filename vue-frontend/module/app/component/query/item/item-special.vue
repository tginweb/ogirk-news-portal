<template>
  <div class="query-item" v-bind="bind">
    <div :class="elmContent.class" class="i-content">
      <div :class="elmRow.class" class="i-row row q-col-gutter-md">

        <div class="col-24 col-md-5">
          <div class="i-media">

            <div class="i-terms -style1 q-ma-md q-px-xs" v-if="termsOutput.length>0">
              <span :key="term._id" class="i-terms-item" v-for="term of termsOutput.slice(0,1)">
                {{term.name}}
              </span>
            </div>

            <div class="i-thumb">
              <component v-bind="bindLink">
                <img
                  v-lazy="itemImageThumbSrc"
                  class="i-thumb__image"
                />
              </component>
            </div>

          </div>
        </div>

        <div class="col-24 col-md-19">
          <div class="i-info">

            <component v-bind="bindLink">

              <h3 class="i-title q-ma-none q-mb-md s-font-xxl">
                {{itemTitle}}
              </h3>

              <div class="i-excerpt q-mb-md text-grey-9 s-font-lg" v-html="itemExcerptFormatted"/>

            </component>

            <div class="row q-col-gutter-md no-wrap">
              <div class="col col-grow">
                <div class="i-meta">
                  <div class="i-meta-date">
                    {{itemDateFormatted}}
                  </div>
                </div>
              </div>
              <div class="col text-right">
                <div class="i-geo" v-if="termsTerritory.length">

                  <q-icon name="fas fa-map-marker-alt q-mr-xs" style="vertical-align: baseline;"/>

                  <router-link
                    :key="term._id"
                    :to="term.url"
                    v-for="term of termsTerritory"
                  >
                    <span style="text-decoration:underline;">{{term.name}}</span>
                  </router-link>

                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import CItem from './_item-post'

  export default {
    name: 'item-special',
    extends: CItem,
    data() {
      return {}
    },
    methods: {}
  }
</script>

<style lang="scss" scoped>

  .i-excerpt {
    font-size: 17px;
  }

  .i-info {
    width: 100%;
    padding: 0;
    bottom: 0;
    box-sizing: border-box;
  }

  .i-media {
    position: relative;

    color: #FFF;

    a {
      text-decoration: none;
      color: #FFF;
    }
  }

  .i-title {
    font-weight: 700;
    line-height: 1.3em;
  }

  .i-meta-date {
    color: #777;
  }

</style>
