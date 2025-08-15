export function boot({Vue}) {

  Vue.component('ad-zone', require('~module/ad/component/ad-zone').default);
  Vue.component('ad-zone-slider', require('~module/ad/component/ad-zone-slider').default);

  Vue.component('query-items-ad', require('~module/ad/component/items-ad').default);

  Vue.component('query-item-ad-constructor', require('~module/ad/component/item-ad-constructor').default);
  Vue.component('query-item-ad-image', require('~module/ad/component/item-ad-image').default);
  Vue.component('query-item-ad-demo', require('~module/ad/component/item-ad-demo').default);
  Vue.component('query-item-ad-teaser-demo', require('~module/ad/component/item-ad-teaser-demo').default);

}

export function request({Vue, router}) {

}
