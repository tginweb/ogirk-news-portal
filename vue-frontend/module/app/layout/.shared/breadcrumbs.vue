<template>

  <div class="com q-mb-xl">

    <ul>
      <li
        v-for="(item, index) of $store.getters['app/menuItemsContext']($route.path, ['primary', 'top'])"
        :key="index"
      >
        <router-link :to="item.url">{{item.title}}</router-link>
      </li>
    </ul>

  </div>

</template>

<script>

  export default {
    props: {

    },
    data() {
      return {}
    },
  }

</script>

<style lang="scss" scoped>


</style>
