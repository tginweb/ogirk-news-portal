<template>

  <div
    :class="{
     'demo-selected': isDemoSelectedZone
   }"
    :id="'ad-zone-' + zone.slug"
    class="c-zone"
    v-if="items.length || $slots.empty"
  >
    <slot
      v-bind="{items}"
      v-if="items.length"
    />

    <template v-if="!items.length && $slots.empty">

      <slot name="empty"/>

    </template>

  </div>

</template>

<script>

  let demoIndex = 0;

  export default {
    props: {
      ads: {},
      zoneCode: {},
      limit: {}
    },
    computed: {

      isDemo() {
        return !!this.$route.query.ads_demo
      },

      isDemoSelectedZone() {
        return this.$store.state.ad.adDemoBannerZone === this.zone.slug
      },

      zone() {
        return this.$store.getters['ad/adZonesBySlug'][this.zoneCode] || {meta: {}}
      },

      items() {
        if (this.isDemo && !this.zone.meta.zone_private) {
          return [this.createDemoItem()]
        } else {
          return this.ads && (this.limit ? this.ads.slice(0, this.limit) : this.ads).map(item => this.createItem(item)) || []
        }
      }
    },
    methods: {

      createDemoItem() {
        demoIndex++;
        return {
          _id: 'demo' + demoIndex,
          nid: demoIndex,
          is: 'query-item-ad-demo',
          title: this.zone.slug,
          meta: {
            banner_height: this.screen.gt.md ? this.zone.meta.zone_height : (this.zone.meta.zone_height_mobile || this.zone.meta.zone_height)
          }
        }
      },

      createItem(item) {
        return {
          ...item,
          is: item.meta.sm_ad_format ? 'query-item-ad-' + item.meta.sm_ad_format : ''
        }
      }
    }
  }
</script>

<style lang="scss" scoped>

  .c-zone {

    &.demo-selected {

      /deep/ {
        .i-content {
          background-color: #ff2222 !important;
        }
      }

    }
  }

</style>
