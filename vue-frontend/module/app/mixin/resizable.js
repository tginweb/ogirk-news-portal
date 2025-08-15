export default {
  data() {
    return {
      windowSize: {
        width: null,
        height: null
      },
    }
  },
  created() {
    if (typeof window !== 'undefined') {
      this._resizableUpdate()
      window.addEventListener('resize', this._resizableUpdate);
    }
  },
  beforeDestroy() {
    if (typeof window !== 'undefined') {
      window.removeEventListener('resize', this._resizableUpdate);
    }
  },
  methods: {
    _resizableUpdate() {
      this.windowSize.width = window.innerWidth
      this.windowSize.height = window.innerHeight

      if (this.resizableUpdate) {
        this.resizableUpdate()
      }
    }
  }
}
