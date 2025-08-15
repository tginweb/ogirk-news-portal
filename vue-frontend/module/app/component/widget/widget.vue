<template>

  <div class="widget">

    <div class="w-header items-center" v-if="!bodyOnly">

      <div class="w-header__title">
        <router-link :to="moreUrl" v-if="moreUrl" >
          {{title}}
        </router-link>
        <span v-else>
           {{title}}
        </span>
      </div>

      <slot name="header-side"></slot>

      <div class="q-gutter-md" v-if="haveNext || havePrev">
        <q-btn
          @click="$emit('prev')" color="primary" dense icon="arrow_left" round size="md" outline
          text-color1="white"
          v-if="havePrev"
        />
        <q-btn
          @click="$emit('next')" color="primary" dense icon="arrow_right" round size="md" outline
          text-color1="white"
          v-if="haveNext"
        />
      </div>

      <div class="w-header__more" v-if="moreUrl && moreSide !== false">
        <router-link :to="moreUrl">
          {{moreLabel}}
          <q-icon name="fas fa-angle-right" v-if="true" size="xs"/>
        </router-link>
      </div>

    </div>

    <div class="w-body">

      <template v-if="loading">
        <el-skeleton v-if="skeleton" :type="skeletonType" :skeletonCount="skeletonCount"/>
      </template>
      <slot v-else></slot>

    </div>

  </div>


</template>

<script>

  export default {
    components: {},
    props: {
      title: String,
      loading: Boolean,
      skeleton: {default: true},
      skeletonType: {default: 'text'},
      skeletonCount: {default: 4},
      moreUrl: {},
      moreLabel: {},
      moreSide: {default: true},
      haveNext: {default: false},
      havePrev: {default: false},
      bodyOnly: {default: false},
    },
    data() {
      return {}
    },
    methods: {},
    computed: {

      skeletonEnable() {
        return this.skeleton
      }
    },
  }
</script>

<style lang="scss" scoped>


</style>
