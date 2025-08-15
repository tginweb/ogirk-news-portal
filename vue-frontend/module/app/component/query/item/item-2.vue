<template>
  <div class="query-item" v-bind="bind">

    <div class="i-content">

      <query-item-ad-teaser-demo :demo="demo" v-if="demo"/>

      <div class="i-media">

        <q-btn
          :label="formatInfo.label"
          class="i-format style-1"
          dense
          size="md"
          v-if="formatInfo"
        >
          <q-icon :name="formatInfo.icon" class="q-ml-sm" size="18px"/>
        </q-btn>

        <div class="i-thumb">

          <component v-bind="bindLink">

            <div class="i-thumb__holder">
              <div class="i-thumb__res__holder bg-grey-3" v-bind="bindThumbResponsive"></div>
              <div class="i-thumb__res__bimg" v-lazy:background-image="itemImageThumbSrc"/>
            </div>

          </component>

        </div>

      </div>

      <div class="i-info q-pa-md">

        <component v-bind="bindLink">
          <h3 :class="elmTitle.class" class="i-title q-ma-none">
            {{itemTitle}}
          </h3>
        </component>

        <div class="i-meta flex items-center q-mt-sm q-pt-sm" v-if="elmMeta.enable">

          <div class="i-date">
            {{itemDateFormatted}}
          </div>

          <q-space></q-space>

          <CShare
            :flat="true"
            :sharing="shareData"
          />

        </div>

      </div>

    </div>
  </div>
</template>

<script>
  import CItem from './_item-post'
  import CShare from './meta/share-dropdown'

  export default {
    name: 'item-2',
    extends: CItem,
    components: {
      CShare
    },
    data() {
      return {
        elementDefaults: {
          title: {
            defclass: 's-font-md'
          },
          meta: {
            enable: true
          },
          excerpt: {
            limit: 200
          },
        }
      }
    },
    methods: {
      tm() {
        return Date.now()
      }
    }
  }
</script>

<style lang="scss" scoped>

  .i-content {
    border: 1px solid #e8e8e8;
    border-radius: 4px;
    height: 100%;

    display: flex;
    flex-direction: column;
    position: relative;
  }

  .i-media {
    color: #fff;

    a {
      color: #fff;
    }
  }

  .i-info {
    width: 100%;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
  }

  .i-title {
    font-weight: 600;
    line-height: 1.4em;
  }

  .i-meta {
    margin-top: auto;
    color: #a1a1a1;
    font-size: 14px;
  }

</style>
