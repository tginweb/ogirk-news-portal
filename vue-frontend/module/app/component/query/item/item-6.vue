<template>
  <div class="query-item" v-bind="bind">
    <div class="i-content">

      <div class="i-media" v-if="itemImageThumbSrc">
        <div class="i-thumb">
          <router-link :to="compUrl">

            <div class="i-thumb__holder">
              <div class="i-thumb__res__holder" v-bind="bindThumbResponsive"></div>
              <div class="i-thumb__res__bimg" v-lazy:background-image="itemImageThumbSrc"/>
            </div>

          </router-link>
        </div>
      </div>

      <div class="i-info q-py-lg q-px-lg">

        <q-btn color="white" text-color="red-8" fab class="i-format" v-if="formatInfo">
          <q-icon :name="formatInfo.icon"/>
        </q-btn>

        <component v-bind="bindLink">
          <h3 class="i-title q-ma-none">
            {{itemTitle}}
          </h3>
        </component>

        <div class="i-excerpt q-mt-sm text-grey-8" v-html="itemExcerptFormatted"/>

      </div>

    </div>
  </div>
</template>

<script>
  import CItem from './_item-post'

  export default {
    name: 'item-6',
    extends: CItem,
    data() {
      return {}
    },
    methods: {}
  }
</script>

<style lang="scss" scoped>

  .query-item {
    border: 1px solid #DDD;
    border-radius: 5px;
  }

  .i-info {
    position: relative;;
  }

  .i-title {
    font-size: 23px;
    font-weight: 700;
    line-height: 1.3em;
  }

  .i-excerpt {
    font-size: 17px;
  }

  .i-format {
    position: absolute;
    top: -30px;
    right: 20px;
  }

</style>
