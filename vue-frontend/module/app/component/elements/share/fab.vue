<template>
  <div>
    <ShareNetwork
      v-for="network in networks"
      :network="network.network"
      :key="network.network"
      :url="sharing.url"
      :title="sharing.title"
      :description="sharing.description"
      :quote="sharing.quote"
      :hashtags="sharing.hashtags"
      :twitterUser="sharing.twitterUser"
      :class="itemClass"
    >
      <q-icon :name="network.icon"></q-icon>
    </ShareNetwork>
  </div>
</template>

<script>
  export default {
    props: {
      itemClass: {},
      itemIconClass: {},
      sharing: {}
    },
    data () {
      return {
        networks: [
          { network: 'odnoklassniki', name: 'Odnoklassniki', icon: 'fab fah fa-lg fa-odnoklassniki', color: '#ed812b' },
          { network: 'telegram', name: 'Telegram', icon: 'fab fah fa-lg fa-telegram-plane', color: '#0088cc' },
          { network: 'vk', name: 'Vk', icon: 'fab fah fa-lg fa-vk', color: '#4a76a8' },
        ]
      }
    }
  }
</script>
<style lang="scss" scoped>

  a {
    color: currentColor;
    cursor: pointer;
  }

</style>
