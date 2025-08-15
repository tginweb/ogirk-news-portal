<template>
  <div class="query-item" v-bind="bind">

    <query-item-ad-teaser-demo :demo="demo" v-if="demo"/>

    <div :class="elmContent.class" class="i-content row">
      <div class="col-24">
        <router-link :to="compUrl">

          <div :class="elements.title && elements.title.class" class="i-title q-ma-none">

            {{itemTitle}}

            <q-btn
              :label="formatInfo.label"
              class="i-format style-2"
              dense
              size="md"
              style="pointer-events: none"
              v-if="formatInfo"
            >
              <q-icon :name="formatInfo.icon" class="q-ml-sm" size="11px"/>
            </q-btn>

          </div>

          <div class="i-date q-mt-sm" v-if="elements.date">
            {{itemDateFormatted}}
          </div>

        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
  import CItem from './_item-post'

  export default {
    name: 'item-4',
    extends: CItem,
    data() {
      return {}
    },
    methods: {}
  }
</script>

<style lang="scss" scoped>

  .query-item {

    &:first-child {
      .i-content {
        padding-top: 0;
      }
    }
  }


  .i-info {
    width: 100%;
    padding: 14px;
    bottom: 0;
    box-sizing: border-box;
  }

  .i-title {
    line-height: 1.3em;
  }

  .i-date {
    color: #888;
    font-size: .8rem;
  }

</style>
