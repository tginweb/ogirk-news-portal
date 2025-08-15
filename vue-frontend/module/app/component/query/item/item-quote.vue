<template>
  <div class="query-item" v-bind="bind">

    <component v-bind="bindLink">
      <div class="i-speaker-text">«{{item.meta.sm_quote_text}}»</div>
    </component>

    <div class="i-hr"></div>

    <div :class="elmContent.class" class="i-content">
      <div :class="elmRow.class" class="i-row row no-wrap">
        <div class="col-7">
          <div class="i-media">
            <div class="i-thumb">
              <component v-bind="bindLink">
                <q-responsive
                  :ratio="1"
                  v-lazy:background-image="$image.resolveUrl(item.meta.sm_quote_speaker_foto, 200)"
                  class="i-thumb-bg"
                />
              </component>
            </div>
          </div>
        </div>
        <div class="col-17 q-ml-md">
          <div class="i-info">
            <component v-bind="bindLink">
              <h4 class="i-speaker-name q-ma-none text-grey-7">
                {{item.meta.sm_quote_speaker_name}}
              </h4>
              <div class="i-speaker-job q-ma-none text-grey-7">
                {{item.meta.sm_quote_speaker_job}}
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
    name: 'item-quote',
    extends: CItem,
    props: {},
    data() {
      return {}
    },
    methods: {},
    computed: {}
  }
</script>

<style lang="scss" scoped>

  .i-speaker-text {
    padding: 0;
    margin: 0 0 15px;
    font-size: 18px;
    line-height: 1.45;
    color: #000;
    font-weight: 700;
  }

  .i-hr {
    margin: 0 0 14px;
    height: 1px;
    background: #ccc;
    position: relative;

    &:before,
    &:after {
      content: '';
      display: block;
      width: 0;
      height: 0;
      position: absolute;
    }

    &:before {
      left: 43px;
      top: 0;
      border-left: 11px solid #ccc;
      border-bottom: 10px solid transparent;
    }

    &:after {
      left: 44px;
      top: -2px;
      border-left: 11px solid #fff;
      border-bottom: 10px solid transparent;
    }
  }

  .i-speaker-name {
    font-size: 17px;
    font-weight: 700;
    margin: 0 0 2px;
    line-height: 1.5;
  }

  .i-speaker-job {
    font-size: 16px;
    font-weight: 400;
    line-height: 1.5;
  }

  .i-info {
    width: 100%;
    padding: 0;
    bottom: 0;
    box-sizing: border-box;
  }

  .i-thumb {
    border-radius: 50%;
    overflow: hidden;
  }

  .i-title {
    font-size: 17px;
    font-weight: 400;
    line-height: 1.3em;
  }

  .i-thumb-bg {
    background-size: cover;
  }
</style>
