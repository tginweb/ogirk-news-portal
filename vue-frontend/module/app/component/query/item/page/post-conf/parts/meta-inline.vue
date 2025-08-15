<template>
  <div class="com row items-center q-col-gutter-md">

    <div class="col-24 col-lg-auto ">
      <q-tabs
        :align="screen.gt.md ? 'left' : 'center'"
        class="c-tags-tabs"
        dense
      >
        <q-route-tab
          :key="term._id"
          :label="'#' + term.name"
          :to="term.url"
          class="q-pl-none q-pr-md"
          v-for="term of hashtags"
        />
      </q-tabs>
    </div>

    <div class="col-24 col-lg-auto q-ml-auto s-align-center s-align-md-right">

      <div class="c-date">
        {{created}}
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
      created: {}
    },
    data() {
      return {}
    },
    mounted() {

    },
    computed: {

      hashtags() {

        const result = [];

        Array.prototype.push.apply(result, this.termsCategory);
        Array.prototype.push.apply(result, this.termsTags);

        return result
      }
    }
  }
</script>

<style lang="scss" scoped>


</style>
