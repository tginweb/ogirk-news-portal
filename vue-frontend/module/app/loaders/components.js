export function boot({Vue}) {
  //useComponent(Vue, 'el-input-address', require('@common/quasar/component/q-ext-input-address').default, process.env.config.DADATA)

  Vue.mixin(require('@common/core/mixin/common').default);
  Vue.mixin(require('~module/app/mixin/common').default);
  Vue.mixin(require('~module/app/mixin/screen').default);
  //Vue.mixin(require('@common/core/mixin/sizable').default);

 // Vue.component('cache-fragment', require('~module/app/component/cache-fragment').default);

  Vue.component('el-widget', require('~module/app/component/widget/widget').default);

  Vue.component('el-share', require('~module/app/component/elements/share/default').default);
  Vue.component('el-skeleton', require('~module/app/component/elements/progress/skeleton').default);
  Vue.component('el-skeleton-wrapper', require('~module/app/component/elements/progress/skeleton-wrapper').default);
  Vue.component('el-spinner', require('~module/app/component/elements/progress/spinner').default);

  Vue.component('ad-zone', require('~module/ad/component/ad-zone').default);
  Vue.component('el-submenu', require('~module/app/layout/.shared/submenu').default);
  Vue.component('el-page-header', require('~module/app/layout/.shared/page-header').default);

  Vue.component('el-gallery', require('~module/app/component/media/gallery').default);
  Vue.component('el-player-video', require('~module/app/component/media/player-video').default);

  Vue.component('query-view', require('@common/query/component/view/view').default);

  Vue.component('query-items', require('@common/query/component/items/items').default);
  Vue.component('query-items-grid', require('@common/query/component/items/items-grid').default);
  Vue.component('query-items-regional', require('@common/query/component/items/items-regional').default);
  Vue.component('query-items-slider', require('~module/app/component/query/items/items-slider').default);
  Vue.component('query-items-swiper', require('~module/app/component/query/items/items-swiper').default);

  Vue.component('query-item', require('@common/query/component/item/item').default);
  Vue.component('query-item-1', require('~module/app/component/query/item/item-1').default);
  Vue.component('query-item-2', require('~module/app/component/query/item/item-2').default);
  Vue.component('query-item-3', require('~module/app/component/query/item/item-3').default);
  Vue.component('query-item-4', require('~module/app/component/query/item/item-4').default);
  Vue.component('query-item-5', require('~module/app/component/query/item/item-5').default);
  Vue.component('query-item-6', require('~module/app/component/query/item/item-6').default);
  Vue.component('query-item-7', require('~module/app/component/query/item/item-7').default);
  Vue.component('query-item-note', require('~module/app/component/query/item/item-note').default);
  Vue.component('query-item-index', require('~module/app/component/query/item/item-index').default);

  Vue.component('query-item-term', require('~module/app/component/query/item/item-term').default);
  Vue.component('query-item-special', require('~module/app/component/query/item/item-special').default);
  Vue.component('query-item-feed', require('~module/app/component/query/item/item-feed').default);
  Vue.component('query-item-search', require('~module/app/component/query/item/item-search').default);
  Vue.component('query-item-issuepub', require('~module/app/component/query/item/item-issuepub').default);
  Vue.component('query-item-district-issuepub', require('~module/app/component/query/item/item-district-issuepub').default);

  Vue.component('query-item-quote', require('~module/app/component/query/item/item-quote').default);
  Vue.component('query-item-topic', require('~module/app/component/query/item/item-topic').default);
  Vue.component('query-item-fotoreport', require('~module/app/component/query/item/item-fotoreport').default);
  Vue.component('query-item-video-player', require('~module/app/component/query/item/item-video-player').default);
  Vue.component('query-item-video-teaser', require('~module/app/component/query/item/item-video-teaser').default);

  Vue.component('query-item-hub-banner-row', require('~module/app/component/query/item/item-hub-banner-row').default);
  Vue.component('query-item-hub-banner-col', require('~module/app/component/query/item/item-hub-banner-col').default);

  Vue.component('query-pager-loadmore', require('@common/query/component/pager/pager-loadmore').default);

}

export function request({Vue, router}) {

}
