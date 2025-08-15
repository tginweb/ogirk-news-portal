export default function () {
  return {
    siteMode: '',
    specialMode: false,

    terms: [],

    menu: [],
    menuItems: [],

    contextLoaded: false,

    page: {
      breadcrumbs: [],
      viewModes: [],
      viewMode: null,
    },

    dialog: {
      'drawer-left': {value: true},
      search: {value: false},
      player: {value: false, props: {source: {}}, state: {playing: false, progress: 0}},
      login: {value: false},
      mview: {value: false},
      gallery: {value: false, props: {source: {}}, state: {}},
      stream: {value: false, props: {source: {}}, state: {}},
      lightbox: {value: false},
      contacts: {value: false},
    },

    cacheFragmentData: {},

    cacheFragmentQueries: {},
  }
}
