<template>

  <div class="com" v-if="items.length">

    <q-tabs
      align="left"
      dense
    >
      <q-route-tab
        :key="index"
        :label="item.title"
        :to="item.url"
        class=""
        v-for="(item, index) of items"
      />
    </q-tabs>

  </div>

</template>

<script>

  export default {
    props: {},
    data() {
      return {}
    },
    computed: {
      items() {
        return this.$store.getters['app/menuItemsContext'](this.$route.path, ['primary', 'top'])
      }
    }
  }

</script>

<style lang="scss" scoped>


</style>
