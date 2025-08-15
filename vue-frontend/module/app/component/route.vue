<template>
  <q-page class="q-mt-lg">

  </q-page>
</template>

<script>
  export default {
    data() {
      return {
        page: {},
        routeFetchCacheTtl: 200,
        routeFetchTimestamp: null
      }
    },

    meta() {

      const appTitle = this.$config.get('APP.TITLE')
      const appCanonical = this.$config.get('APP.CANONICAL')
      const appDefaultImage = this.$config.get('APP.IMAGE')

      const data = {
        title: this.pageTitle,
        titleTemplate: title => `${title} - ${appTitle}`,
        meta: [
          {'property': 'description', 'content': this.ogPageDesc},
          {'property': 'keywords', 'content': this.ogPageKeywords},
          {'property': 'og:title', 'content': this.ogPageTitle},
          {'property': 'og:description', 'content': this.ogPageDesc},
          {'property': 'og:keywords', 'content': this.ogPageKeywords},
        ]
      }

      if (this.ogPageImage) {
        data.meta.push({'property': 'og:image', 'content': this.ogPageImage})
      } else {
        data.meta.push({
          'property': 'og:image',
          'content': appDefaultImage
        })
      }

      if (this.ogPageUrl) {
        data.meta.push({'property': 'og:url', 'content': this.ogPageUrl})
      }

      data.link = {}

      if (this.pageCanonical) {
        data.link.canonical = {rel: 'canonical', href: appCanonical + this.pageCanonical}
      }

      return data
    },

    methods: {
      pageStyle(offset) {
        return {minHeight: `calc(100vh - 200px)`}
      }
    },

    computed: {

      pageTitle() {

        if (this.pageData) {
          if (this.pageData.title) {
            return this.pageData.title
          } else if (this.pageData.entity) {
            return this.pageData.entity.name || this.pageData.entity.title
          }
        }
        return this.page.title
      },

      ogPageTitle() {

        if (this.pageData) {
          if (this.pageData.title) {
            return this.pageData.title
          } else if (this.pageData.entity) {
            return this.pageData.entity.share && this.pageData.entity.share.title || this.pageData.entity.name || this.pageData.entity.title
          }
        }
        return this.page.title
      },

      ogPageDesc() {
        if (this.pageData) {
          if (this.pageData.entity) {
            if (this.pageData.entity.meta.description) {
              return this.pageData.entity.meta.description
            }
            return this.pageData.entity.share && this.pageData.entity.share.description || this.pageData.entity.excerpt
          }
        }
        return this.page.description
      },

      ogPageKeywords() {
        if (this.pageData) {
          if (this.pageData.entity) {
            if (this.pageData.entity.meta.keywords) {
              return this.pageData.entity.meta.keywords
            }
          }
        }
      },


      ogPageImage() {
        if (this.pageData) {
          if (this.pageData.entity) {
            return this.pageData.entity.share && this.pageData.entity.share.image
          }
        }
        return this.page.image
      },

      ogPageUrl() {
        return this.pageData && this.pageData.entity && this.pageData.entity.url
      },

      pageCanonical() {
        return this.pageData && this.pageData.entity && this.pageData.entity.url
      }
    }

  }
</script>
