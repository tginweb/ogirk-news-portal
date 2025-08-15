<template>
  <div class="com">

    <q-tabs class="c-tags__tabs q-mb-sm" dense align="left" :mobileArrows="true">
      <q-route-tab
        :to="term.url"
        :label="'#' + term.name"
        v-for="term of termsCategory.concat(termsTags)"
        :key="term._id"
        class="q-pl-none q-pr-md"
      />
    </q-tabs>

    <div class="q-mb-sm text-grey-8" v-if="textAuthor.length">

      <div class="inline-block q-mr-sm">
        Текст:
      </div>

      <span
        :key="index"
        v-for="(item, index) of textAuthor"
      >
        <router-link :to="item.url" v-if="item.url" class="text-grey-8">{{item.title}}</router-link>
        <span class="" v-else>{{item.title}}</span>{{index!==textAuthor.length-1 ? ',' : ''}}
      </span>

    </div>

    <div class="q-mb-sm text-grey-8" v-if="fotoAuthor.length">

      <div class="inline-block q-mr-sm">
        Фото:
      </div>

      <span
        :key="index"
        v-for="(item, index) of fotoAuthor"
      >
        <router-link :to="item.url" v-if="item.url" class="text-grey-8">{{item.title}}</router-link>
        <span class="" v-else>{{item.title}}</span>{{index!==textAuthor.length-1 ? ',' : ''}}
      </span>

    </div>


    <div class="flex items-center">

      <div class="c-date text-grey-7">
        {{$util.date.timestampToFormat(item.created, 'datetime')}}
      </div>

      <div
        class="c-social q-ml-auto"
      >
        <div class="c-social__inner">
          <el-share
            :sharing="share"
            class="q-gutter-sm"
          />
        </div>
      </div>

    </div>


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

      }
    },
    mounted() {

    },
    computed: {
    }
  }
</script>

<style lang="scss" scoped>

  .c-social {
    /deep/ {
      .c-item {
        padding: 6px !important;
      }
      .c-item-icon {
        font-size: 15px !important;;
      }
    }
  }

</style>
