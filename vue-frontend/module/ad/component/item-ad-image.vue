<template>
  <div class="query-item" v-bind="bind">

    <div class="i-content text-center">


      <div class="i-media">
        <div class="i-thumb">

          <component v-bind="bindLink">


            <img
              v-lazy="imageSrc"
              class="i-thumb__img"
              style="width: 100%;"
            />

          </component>

        </div>
      </div>

    </div>

  </div>
</template>

<script>
  import CItem from '~module/app/component/query/item/_item-post'
  import MItemBannered from '~module/app/mixin/item-bannered'

  export default {
    name: 'item-ad-banner',
    extends: CItem,
    mixins: [MItemBannered],
    data() {
      return {}
    },
    methods: {},
    computed: {

      compUrl() {

        let url = this.item.meta.sm_ad_link || ''

        if (this.item.meta.er_token) {
          url = url.indexOf('?') !== -1 ? url + '&erid=' + this.item.meta.er_token : url + '?erid=' + this.item.meta.er_token
        }

        return url
      },

      imageSrc() {
        return 'https://www.ogirk.ru' + this.item.meta.sm_ad_image;
      }

    }
  }
</script>

<style lang="scss" scoped>

  .i-content {
    height: 100%;
  }

  .i-media {

  }


</style>
