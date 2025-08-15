const useragent = require('express-useragent')

let screenUpdated = false

export default {
  props: {},

  computed: {

    isMobile() {
      return this.screen.lt.sm;
    },

    isDesktop() {
      return this.screen.gt.sm;
    },

    screen() {

      if (process.env.SERVER) {

        if (this.$q.ssrContext && this.$q.ssrContext.req.headers) {

          if (this.$store.state.screen.serverScreen) return this.$store.state.screen.serverScreen

          const headers = this.$q.ssrContext.req.headers

          const ua = useragent.parse(headers['user-agent']);

          if (!ua.isMobile) {
            const res = {
              name: 'lg',
              lt: {
                sm: false,
                md: false,
                lg: false,
                xl: true
              },
              gt: {
                xs: true,
                sm: true,
                md: true,
                lg: false,
              }
            }

            this.$store.commit('screen/setServerScreen', res)

            return res
          }

        }

        return this.$q.screen

      } else {

        if (!this.$q.screen.width) {

          if (!screenUpdated) {
            screenUpdated = true
            this.$store.commit('screen/update')
          }

          return this.$store.getters['screen/screen']
        }

        return this.$q.screen
      }
    }

  },

}

