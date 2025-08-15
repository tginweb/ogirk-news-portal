<template>

  <div class="com">

    <instagram-embed
      :url="data.instagram"
      :max-width=700
    />

  </div>

</template>

<script>
  import InstagramEmbed from 'vue-instagram-embed';

  export default {
    components: {
      InstagramEmbed
    },
    props: {
      data: {}
    },
    data() {
      return {}
    },
    computed: {

    }
  }

</script>

<style lang="scss" scoped>


</style>
