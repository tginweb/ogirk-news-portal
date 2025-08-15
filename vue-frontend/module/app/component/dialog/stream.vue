<template>

  <q-dialog
    :seamless="true"
    :value="visible"
    @input="onVisibleChange"
    content-class="com-dialog-stream"
    position="bottom"
  >
    <div
      class="dialog"
      v-bind="bindDialog"
    >

      <vue-plyr
        :options="playerOptionsAudio"
        ref="playerBg"
        v-show="false"
      >
        <audio controls crossorigin playsinline>
          <source
            src="/statics/news.mp3"
            type="audio/mp3"
          />
        </audio>
      </vue-plyr>

      <div
        class="player-audio flex items-center"
        v-if="currentPost"
      >

        <div
          @mouseout="onHoverChange(false)"
          @mouseover="onHoverChange(true)"
          class="player-audio__player"
          v-show="hover"
        >
          <div class="flex no-wrap items-center">

            <div class="full-height flex no-wrap" style="width: calc(100% - 58px)">

              <q-btn
                :icon="$icons.fasChevronLeft"
                @click="onPrev"
                dense
                flat
                size="12px"
                v-if="currentIndex>0"
              />

              <div
                :style="{
                  width: currentIndex > 0 ? 'calc(100% - 70px)' : 'calc(100% - 35px)'
                }"
                class="q-mx-sm"
              >

                <div class="player-audio__desc q-mb-xs s-font-sm">
                  <marquee-text>
                    <router-link :to="currentPost.url" class="q-mr-xl">{{currentPost.title}}</router-link>
                  </marquee-text>
                </div>

                <div v-if="!currentPost.loading">

                  <vue-plyr
                    :options="playerOptionsAudio"
                    class="player-control"
                    ref="player"
                    v-if="currentSrc"
                  >
                    <audio controls crossorigin playsinline>
                      <source
                        :src="currentSrc"
                        type="audio/mp3"
                      />
                    </audio>
                  </vue-plyr>

                </div>
                <q-skeleton class="q-my-auto" type="text" v-else/>

              </div>

              <q-btn
                :icon="$icons.fasChevronRight"
                @click="onNext"
                dense
                flat
                size="12px"
              />

            </div>

            <q-btn
              @click="visible = false"
              class="player-audio__closer q-py-auto q-mx-sm"
              dense
              flat
              icon="close"
              size="17px"
            />

          </div>

        </div>

        <div>

          <q-btn
            :disable="currentPost.loading"
            :icon="playing ? 'pause' : 'play_arrow'"
            @click="onTogglePlay"
            @mouseout="onHoverChange(false)"
            @mouseover="onHoverChange(true)"
            class="player-audio__toggle "
            color="primary"
            dense
            round
            size="19px"
          />

        </div>

      </div>

    </div>

  </q-dialog>

</template>

<script>
  import CDialog from '@common/dialog/component/dialog'
  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";
  import MarqueeText from '~module/app/component/ui/MarqueeText'

  export default {
    extends: CDialog,
    components: {
      MarqueeText
    },
    apollo: {
      posts: generateQueryInfo('posts', require('~module/entity/graphql/getPosts.gql'), {}),
    },
    props: {},
    data() {
      return {
        dialogModule: 'app',
        dialogName: 'stream',

        queries: {
          posts: {
            result: null,
            vars: {
              filter: {
                type: 'post',
              },
              nav: {
                limit: 20
              }
            }
          }
        },

        items: [],
        currentIndex: null,
        currentSrc: null,

        volume: 0.1,
        playing: false,
        playingBg: true,

        hover: false,
        hoverTimeout: null,
        visibleProxy: this.$store.getters['dialogVisible']('app/stream'),

        playerBgInited: false,
        playerInited: false,

        bgPlayerInitInterval: false
      }
    },
    created() {

    },
    computed: {

      currentPost() {
        return this.currentIndex !== null ? this.items[this.currentIndex] : null
      },

      bindDialog() {

        const res = {
          style: {},
          class: {}
        }

        res.class['theme-white'] = true
        res.class['size-minimized'] = true

        return res
      },

      playerOptionsAudio() {
        return {
          controls: ['play-large', 'progress', 'captions', 'pip', 'airplay', 'fullscreen', 'volume'],
        }
      },

      player() {
        return this.$refs.plyr ? this.$refs.plyr.player : null
      },

    },
    watch: {

      playingBg(val) {
        const playerBg = this.$refs.playerBg && this.$refs.playerBg.player
        if (playerBg) val ? playerBg.play() : playerBg.pause()
      },

      playing(val) {
        const player = this.$refs.player && this.$refs.player.player
        if (player) val ? player.play() : player.pause()
      },

      'queries.posts.result'(data) {
        this.items = data.nodes
        this.goTo(0)
      },

      visible(val) {
        this.visibleProxy = val
      },

      visibleProxy(val) {

        this.$nextTick(() => {
          this.visible = val
        })

        if (!val) {
          if (this.$refs.plyr && this.$refs.plyr.player)
            this.$refs.plyr.player.destroy()

          /*
          this.$store.dispatch('dialogUpdateState', ['app/player', {
            uid: null,
            file: null,
            playing: false
          }])
           */
        }
      },

      'currentPost.speech_file_teaser'(val) {

        console.log('currentPost.speech_file_teaser')

        if (val) {

          this.currentSrc = null

          this.$nextTick(() => {

            this.currentSrc = val

            this.initPlayer()

            setTimeout(() => {
              this.playing = false
              this.$nextTick(() => {

                this.playing = true

                const playerBg = this.$refs.playerBg && this.$refs.playerBg.player
                playerBg.play()
              })
            }, 1000)

          })

        }
      },

      currentPost(post) {

        this.initBgPlayer()
      }
    },
    methods: {

      onPrev() {
        this.goTo(this.currentIndex - 1)
      },

      onNext() {
        this.goTo(this.currentIndex + 1)
      },

      initPlayer() {

        setTimeout(() => {

          const player = this.$refs.player && this.$refs.player.player

          if (player) {

            player.on('ended', () => {

              console.log('ended')

              this.goTo(this.currentIndex + 1)
            })

            this.playerInited = true

          }

        }, 100)

      },

      initBgPlayer() {

        if (this.playerBgInited) return

        setTimeout(() => {

          const playerBg = this.$refs.playerBg && this.$refs.playerBg.player

          if (playerBg) {

            let firstPlay = true

            playerBg.on('playing', async (event) => {

              if (!firstPlay) return

              setTimeout(async () => {
                // playerBg.volume = 0.1
              }, 500)

              firstPlay = false
            })

            playerBg.on('ended', () => {
              playerBg.play();
            })

            this.playerBgInited = true
          }


        }, 100)

      },

      async goTo(index) {

        if (index > this.items.length - 1 || index < 0) return

        const post = this.items[index]

        if (post) {
          if (!post.speech_file_teaser) {
            this.loadPostSpeech(post);
          }
          this.currentIndex = index
        }
      },

      onTogglePlay() {
        this.playing = !this.playing
        this.playingBg = this.playing
      },

      pause() {
        this.playing = false
        this.playingBg = false
      },

      play() {
        this.playing = true
        this.playingBg = true
      },

      async loadPostSpeech(post) {

        try {
          this.$set(post, 'loading', true)

          const res = await this.$apiWp.get('smart-speech/post-audio-download', {
            params: {
              postId: post.nid,
              mode: 'teaser'
            }
          })

          if (res.data && res.data.url) {
            this.$set(post, 'speech_file_teaser', res.data.url)
          } else {
            this.$set(post, 'speech_file_teaser', false)
          }

        } catch (e) {
          console.log(e)
        }

        this.$set(post, 'loading', false)

      },

      onHoverChange(val) {
        if (this.hoverTimeout) clearTimeout(this.hoverTimeout)
        this.hoverTimeout = setTimeout(() => {
          this.hover = val
        }, val ? 0 : 1000)
      },

      onVisibleChange(val) {
        this.visible = val
      },


    },
    beforeDestroy() {


    },
    mounted() {
      // this.init()


    },
  }

</script>

<style lang="scss">

  .com-dialog-stream {

    .dialog {
      background: transparent;
      box-shadow: none;
    }

    .closer {
      position: fixed;
      right: 10px;
      top: 10px;
      z-index: 100;
      border-radius: 0;
      background: rgba(0, 0, 0, 0.5);
    }

    .dialog__header {
      .dialog__header__title {
        overflow: hidden;
        white-space: nowrap;
      }
    }


    .theme-black {
      .dialog__header {
        background: #000;

        .dialog__header__actions {

        }
      }
    }

    .theme-white {
      .dialog__header {
        background: #000;
        color: #fff;

        .dialog__header__actions {

        }
      }
    }

    .size-minimized {
      margin-left: auto;
      margin-bottom: 20px;
    }

    .player-audio {

      .player-audio__desc {
        white-space: nowrap;
        overflow: hidden;

        a {
          color: #555 !important;
        }
      }

      .player-audio__player {
        border: 1px solid #CCC;
        background-color: #f7f7f7;
        width: 400px;
        margin-right: -45px;
        padding-right: 40px;
        padding-left: 20px;
        border-radius: 50px;
        box-sizing1: content-box;
      }

      /deep/ .plyr__controls {
        background-color: transparent !important;
        padding: 0;
      }
    }
  }

  .player-control {
    /deep/ .plyr__volume {
      display: none;
    }
  }


</style>
