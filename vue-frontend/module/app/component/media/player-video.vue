<template>
  <div class="com">
    <div class="content" v-intersection="onVisible">

      <q-btn
        @click="mplayerOpen('maximized')"
        class="bt-enlarge"
        color="grey-8"
        dense
        icon="open_in_full"
        size="md"
        title="Увеличить"
        v-if="isDesktop"
      />

      <iframe
          :src="media.file"
          width="100%"
          height="440"
          frameborder="0"
          allowfullscreen="allowfullscreen"
          v-if="media.provider=='rutube'"
      ></iframe>

      <iframe
        :src="media.file"
        width="100%"
        height="440"
        frameborder="0"
        allowfullscreen="allowfullscreen"
        v-else-if="media.provider=='vk'"
      ></iframe>

      <vue-plyr
        :options="playerOptionsVideoYoutube"
        ref="plyr"
        v-if="media.provider=='youtube'"
      >
        <div
          :data-plyr-embed-id="media.file"
          data-plyr-provider="youtube"
        ></div>
      </vue-plyr>

      <vue-plyr
        :options="playerOptionsVideoFile"
        ref="plyr"
        v-if="media.provider=='video'"
      >
        <video
          controls
          crossorigin
          playsinline
        >
          <source
            :src="media.file"
            type="video/mp4"
          />
        </video>
      </vue-plyr>


    </div>
  </div>
</template>

<script>

  export default {
    name: 'player-video',
    components: {},
    props: {
      media: {},
      viewoutPlayer: {},
      autoplay: {}
    },
    data() {
      return {}
    },
    mounted() {
      if (!this.$refs.plyr)
        return;

      this.$refs.plyr.player.on('ended', () => this.$emit('ended', this.media))

      this.$refs.plyr.player.on('enterfullscreen', () => {
        document.body.classList.add("plr-fullscreen")
      })

      this.$refs.plyr.player.on('exitfullscreen', () => {
        document.body.classList.remove("plr-fullscreen")
      })

    },
    methods: {

      onVisible(isVisible) {
        return;
      },

      mplayerOpen(mode) {

        setTimeout(() => {

          this.$store.dispatch('dialogShow', ['app/player', {
            sourceComId: this.comId,
            source: this.$util.base.cloneDeep(this.media),
            time: this.$refs.plyr.player.currentTime,
            volume: this.$refs.plyr.player.volume,
            muted: this.$refs.plyr.player.muted,
            playing: this.$refs.plyr.player.playing,
            mode: mode,
            onStart: async (com, player) => {
              return new Promise(async (resolve, reject) => {
                player.currentTime = this.$refs.plyr.player.currentTime
                this.$refs.plyr.player.pause()
                resolve()
              })
            },
            onClose: async (playState) => {
              this.$refs.plyr.player.muted = playState.muted
              this.$refs.plyr.player.volume = playState.volume
              this.$refs.plyr.player.currentTime = playState.time
            },
          }])

        }, 100)

      },

      mplayerClose() {
        return this.$store.dispatch('dialogHide', ['app/player'])
      },

    },
    computed: {
      playerState() {
        return this.$store.state.app.dialog.player
      },

      playerOptionsVideo() {
        return {
          autoplay: this.autoplay
        }
      },

      playerOptionsVideoYoutube() {
        return {
          ...this.playerOptionsVideo
        }
      },

      playerOptionsVideoFile() {
        return {
          ...this.playerOptionsVideo
        }
      }
    },
    watch: {}
  }
</script>

<style lang="scss" scoped>

  .i-content {
    position: relative;
  }

  .bt-enlarge {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 100;
  }

</style>
