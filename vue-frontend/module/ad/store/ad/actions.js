import {Cookies} from 'quasar'

export function setAdZones(context, data) {
  context.commit('setAdZones', data);
}

export function setAdTeaserZones(context, data) {
  context.commit('setAdTeaserZones', data);
}

export function adSelectDemoBannerZone(context, data) {

  context.commit('adSetDemoBannerZone', data);

  this.$util.dom.scrollTo({el: '#ad-zone-' + data, duration: 100})
}

export function adSelectDemoTeaserZone(context, data) {

  context.commit('adSetDemoTeaserZone', data);

  this.$util.dom.scrollTo({el: '#ad-teaser-zone-' + data, duration: 100})
}
