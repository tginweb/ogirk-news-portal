<template>
  <div class="com" ref="com" style="height: 100%;">

    <div
      class="c-speech q-mb-lg q-mt-xs"
      v-if="audioEnable"
    >
      <q-btn
        @click="$store.dispatch('app/playerPlayPostAudio', {item: item, mode: 'full'})"
        class="full-width"
        color="red-5"
        dense
        icon="keyboard_voice"
        label="прослушать"
        outline
      />
    </div>

    <div @click="audioEnable=true" class="c-date q-mb-lg q-mt-sm">
      {{$util.date.timestampToFormat(item.created, 'datetime')}}
    </div>


    <div class="c-cats q-mb-lg" v-if="termsCategory.length">

      <div class="c-cats__label q-mb-sm">
        Рубрики
      </div>

      <router-link
        :key="term._id"
        :to="term.url"
        class="c-cats__item q-mb-sm"
        v-for="term of termsCategory"
      >
        {{term.name}}
      </router-link>

    </div>

    <div class="c-author q-mb-lg" v-if="textAuthor.length">

      <div class="c-author__label q-mb-sm">
        Текст
      </div>

      <span
        :key="index"
        class="c-author__item"
        v-for="(item, index) of textAuthor"
      >
        <router-link :to="item.url" v-if="item.url">{{item.title}}</router-link>
        <span v-else>{{item.title}}</span>{{index!==textAuthor.length-1 ? ',' : ''}}
      </span>

    </div>

    <div class="c-author q-mb-lg" v-if="fotoAuthor.length">

      <div class="c-author__label q-mb-sm">
        Фото
      </div>

      <span
        :key="index"
        class="c-author__item"
        v-for="(item, index) of fotoAuthor"
      >
        <router-link :to="item.url" v-if="item.url">{{item.title}}</router-link>
        <span v-else>{{item.title}}</span>{{index!==fotoAuthor.length-1 ? ',' : ''}}
      </span>

    </div>

    <div class="c-tags q-mb-lg">

      <div class="c-tags__label q-mb-sm">
        Теги
      </div>

      <router-link
        :key="term._id"
        :to="term.url"
        class="c-tags__item "
        v-for="term of termsTags"

      >
        #{{term.name}}
      </router-link>

    </div>

    <q-no-ssr>
    <div
      class="c-social q-gutter-sm"
      v-hc-sticky="{stickTo: $refs.com, top: 60}"
      v-if="isMounted"
    >

      <div class="c-social__inner">
        <el-share
          :sharing="share"
          class="q-gutter-sm"
        />
      </div>

    </div>
    </q-no-ssr>

  </div>
</template>

<script>

  export default {
    props: {
      item: {},
      termsCategory: {},
      termsTags: {},
      textAuthor: {},
      fotoAuthor: {},
      share: {}
    },
    data() {
      return {
        isMounted: false,
        audioEnable: false
      }
    },
    mounted() {
      this.isMounted = true
    },
    computed: {}
  }
</script>

<style lang="scss" scoped>


</style>
