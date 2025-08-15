<template>

  <div class="query-item">

  </div>

</template>

<script>
  import CItem from './_item'
  import {icons} from './../../../loaders/icons'

  const formatsInfo = {
    video: {icon: icons.fasPlay, label: 'видео'},
    gallery: {icon: icons.farImages, label: 'фото'},
    exclusive: {icon: icons.fasStar, label: 'эксклюзив'},
  }

  export default {
    extends: CItem,
    props: {},
    data() {
      return {}
    },
    methods: {},

    computed: {

      demo() {

        if (
          this.$route.query.ads_teaser_demo &&
          this.queryId &&
          this.index &&
          this.$store.getters['ad/teaserZonesByQuery'][this.queryId] &&
          this.$store.getters['ad/teaserZonesByQuery'][this.queryId][this.index]
        ) {
          return this.$store.getters['ad/teaserZonesByQuery'][this.queryId][this.index]
        }

      },

      formatInfo() {

        let format

        switch (this.host.format) {
          case 'video':
          case 'gallery':
            format = this.host.format
            break
          default:
            if (this.host.taxonomy['sm-role'] && this.host.taxonomy['sm-role'].indexOf(6202) > -1) format = 'exclusive'
        }

        return format ? formatsInfo[format] : null
      },

      itemTitle() {
        return this.host.title
      },

      itemSubtitle() {
        return this.item.meta.entity_subtitle
      },

      itemExcerpt() {
        return this.host.excerpt
      },

      itemDate() {
        return this.host.created
      },

      itemTerms() {
        return this.termsCategory.filter(term => {
          if (this.elmTerms.hasOwnProperty('depth')) {
            switch (this.elmTerms.depth) {
              case 0:
              case 1:
              case 2:
              case 3:
                if (term.parent === this.elmTerms.depth)
                  return true;
                break;
              case '>0':
                if (term.parent > 0)
                  return true;
                break;
            }
            return false
          } else if (this.elmTerms.hasOwnProperty('parent')) {
            return term.parent === this.elmTerms.parent
          } else if (this.elmTerms.hasOwnProperty('exclude')) {
            return this.elmTerms.exclude.indexOf(term.nid) === -1
          } else {
            return true
          }
        })
      },

      elmTerms() {
        return this.elementParams('terms')
      },

      elmFormat() {
        return this.elementParams('format')
      },

      termsOutput() {
        //return this.termsTerritory.length ? this.termsTerritory : this.termsCategory
        return this.termsCategory
      },

      terms() {
        return this.host.terms || []
      },

      termsByTax() {
        return this.terms.reduce((map, obj) => ((map[obj.taxonomy] ? map[obj.taxonomy] : map[obj.taxonomy] = []).push(obj), map), {});
      },

      termsCategory() {
        return this.termsByTax['category'] || [];
      },

      termsTags() {
        return this.termsByTax['post_tag'] || [];
      },

      termsAuthor() {
        return this.termsByTax['sm-author'] || [];
      },

      termsTerritory() {
        return this.termsCategory.filter(item => item.parent == 1443)
      },



      termHub() {
        return this.termsByTax['sm-hub-term'] && this.termsByTax['sm-hub-term'].length && this.termsByTax['sm-hub-term'][0];
      },

      termIssue() {
        return this.termsByTax['sm-issue'] && this.termsByTax['sm-issue'].length && this.termsByTax['sm-issue'][0];
      },

      hubRelatedTerms() {
        const res = {}

        if (parseInt(this.item.meta.sm_hub_related_terms_str))
          res['post_tag'] = [parseInt(this.item.meta.sm_hub_related_terms_str)]

        if (this.termHub)
          res['sm-hub-term'] = [this.termHub.nid]

        return res;
      },

      textAuthor() {
        let items = []

        if (this.item.textAuthor && this.item.textAuthor.length) {
          items = this.item.textAuthor.map((node) => ({url: node.url, title: node.name}))
        }

        if (this.item.meta.sm_author_text) {
          items.push({title: this.item.meta.sm_author_text})
        }

        return items
      },

      fotoAuthor() {
        let items = []

        if (this.item.fotoAuthor && this.item.fotoAuthor.length) {
          items = this.item.fotoAuthor.map((node) => ({url: node.url, title: node.name}))
        }

        if (this.item.meta.sm_authorfoto_text) {
          items.push({title: this.item.meta.sm_authorfoto_text})
        }

        return items
      },
    }
  }
</script>

<style lang="scss" scoped>

  .query-item {

  }

</style>
