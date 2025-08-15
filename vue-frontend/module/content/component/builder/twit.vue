<template>

  <div class="com">


  </div>

</template>

<script>
 // import { Tweet, Moment, Timeline } from 'vue-tweet-embed'

  export default {
    components: {
    //  Tweet
    },
    props: {
      data: {}
    },
    data() {
      return {}
    },
    computed: {

      twitId() {
        let mt
        if (mt = this.data.twit.match(/https?\:\/\/twitter\.com\/[^\/]+\/status\/([\d]+)/i)) {
          return mt[1]
        }
      }
    }
  }

</script>

<style lang="scss" scoped>


</style>
