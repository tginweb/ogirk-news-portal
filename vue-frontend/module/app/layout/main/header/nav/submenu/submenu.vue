<template>

    <q-list class="com">
      <q-item clickable v-close-popup :to="item.url" v-for="(item, index) in node.children" :key="index">
        <q-item-section>
         {{item.title}}
        </q-item-section>
      </q-item>
    </q-list>

</template>

<script>
  export default {
    props: {
      node: {},
      value: {}
    },
    data() {
      return {

      }
    },
    watch: {
    },
    computed: {

    }
  }
</script>

<style lang="scss" scoped>

  .com {
    text-align: left;
    min-width: 230px
  }

</style>
