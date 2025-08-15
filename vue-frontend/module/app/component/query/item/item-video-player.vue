<template>
  <div class="query-item">
    <div class="i-content">

      <div class="" >

        <template v-if="1">

          <el-player-video
            :media="source"
            :autoplay="autoplay"
            @ended="onPlayerEnded"
          />

        </template>

        <template v-else>

          <div class="i-media">
            <div class="i-thumb">

              <div class="i-thumb__holder">
                <div class="i-thumb__res__holder bg-grey-3" v-bind="bindThumbResponsive"></div>
                <div class="i-thumb__res__bimg" v-lazy:background-image="itemImageThumbSrc"/>
              </div>

            </div>
          </div>

        </template>

        <h3 class="i-title q-ma-none q-mt-md">
          <router-link :to="compUrl">
            {{itemTitle}}
          </router-link>
        </h3>

        <div class="i-excerpt q-mt-sm gt-md" v-if="itemExcerpt">

          <span v-html="itemExcerptFormatted"></span>

          <router-link :to="compUrl">
            <q-icon name="fas fa-caret-right"/>
            <u>читать далее</u>
          </router-link>

        </div>

        <div class="i-meta flex items-center q-mt-none q-mt-sm-md gt-md" v-if="elements.meta">

          <div class="i-date">
            {{itemDateFormatted}}
          </div>

          <q-space></q-space>

          <CShare
            :flat="true"
            :sharing="shareData"
            label="поделиться"
            size="md"
          />

        </div>

      </div>

    </div>
  </div>
</template>

<script>
  import CItem from './_item-post'
  import CShare from './meta/share-dropdown'

  let inited = false

  export default {
    name: 'item-video-player',
    extends: CItem,
    components: {
      CShare
    },
    props: {
      autoplay: {},
      loadVideo: {default: true}
    },
    data() {
      return {
        reload: false
      }
    },
    mounted() {

    },
    methods: {
      onPlayerEnded(media) {
        this.$emit('playerEnded', media)
      },
      tm() {
        return Date.now()
      },
      dzenGetId(url) {
        const regExp = /(?:[?&]v=|\/embed\/|\/1\/|\/v\/|https:\/\/(?:www\.)?youtu\.be\/)([^&\n?#]+)/;
        const match = url.match(regExp);
        return match && match[1];
      },

      youTubeGetID(url) {
        const regExp = /(?:[?&]v=|\/embed\/|\/1\/|\/v\/|https:\/\/(?:www\.)?youtu\.be\/)([^&\n?#]+)/;
        const match = url.match(regExp);
        return match && match[1];
      },

      getVideoUrl(url) {
        const regex = new RegExp(/(http[s]?(.+?)\.(mp4|mpg|avi))/);
        const match = url.match(regex)
        return match && match[1];
      },

    },
    watch: {
      item() {
        return;

        this.reload = true;
        this.$nextTick(() => {
          setTimeout(()=>{
            this.reload = false;
          }, 1000)

        })
      }
    },
    computed: {
      source() {
        let res = {}, fileId, mt

        if (mt = this.item.contentFormatted.match(/(https\:\/\/vk\.com\/video\_ext.+?)\"/)) {
          res.format = 'video'
          res.provider = 'vk'
          let doc = new DOMParser().parseFromString(mt[1], "text/html");
          res.file = doc.documentElement.textContent;
        } else if (mt = this.item.contentFormatted.match(/(https\:\/\/dzen\.ru\/embed\+?)\"/)) {
          res.format = 'video'
          res.provider = 'vk'
          let doc = new DOMParser().parseFromString(mt[1], "text/html");
          res.file = doc.documentElement.textContent
        } else if (mt = this.item.contentFormatted.match(/(https\:\/\/dzen\.ru\/embed\+?)\"/)) {
          res.format = 'video'
          res.provider = 'vk'
          let doc = new DOMParser().parseFromString(mt[1], "text/html");
          res.file = doc.documentElement.textContent
        } else if (fileId = this.youTubeGetID(this.item.contentFormatted)) {
          res.format = 'video'
          res.provider = 'youtube'
          res.file = fileId
        } else if (fileId = this.getVideoUrl(this.item.contentFormatted)) {
          res.format = 'video'
          res.provider = 'video';
          res.file = fileId
        }

        return res
      }
    }
  }
</script>

<style lang="scss" scoped>

  .i-media {
    position: relative;
    color: #fff;
  }

  .i-title {
    font-size: 20px;
    font-weight: 700;
    line-height: 1.5em;
  }

</style>
