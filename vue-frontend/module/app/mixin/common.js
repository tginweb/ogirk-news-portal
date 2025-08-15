
export default {
  props: {},
  data() {
    return {
      instancePageData: {},
    }
  },
  created() {

  },
  computed: {

    pageDataRouteId() {
      return this.$route.path
    },

    pageRouteData() {
      return this.pageData
    },

    pageData() {

      if (this.context === 'instance') return this.instancePageData

      const pageData = this.$store.state.pageData

      if (pageData.routeId !== this.pageDataRouteId) return {}

      return pageData
    },
  },
  watch: {},
  methods: {


  },
}

