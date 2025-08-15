<template>

  <q-dialog
    :position="!maximized ? 'bottom' : 'standard'"
    :seamless="!maximized"
    :value="visibleProxy"
    @input="onVisibleChange"
    content-class="com-dialog-player"
  >
    <div
      class="dialog"
      v-bind="bindDialog"
    >

      <div v-if="!reset && source.format && source.provider">

        <div
          lass="player-video"
          v-if="source.format === 'video'"
        >
          <div class="dialog__header q-px-sm flex no-wrap items-center">

            <div class="dialog__header__title s-font-sm" style="">

              <marquee-text>
                <router-link
                  :to="itemUrl"
                  class="s-link text-white"
                >
                  {{itemTitle}}
                </router-link>
              </marquee-text>

            </div>

            <div class="dialog__header__actions q-ml-auto col-auto">

              <q-btn
                @click='state.maximized = true'
                color="white"
                dense
                flat
                icon="crop_square"
                size="md"
                v-if='maximizable && !state.maximized'
              >

              </q-btn>

              <q-btn
                @click='state.maximized = false'
                color="white"
                dense
                flat
                icon="minimize"
                size="md"
                v-if='maximizable && state.maximized'
              />

              <q-btn
                @click="visible=false"
                color="white"
                dense
                flat
                icon="close"
                size="md"
              >

              </q-btn>

            </div>

          </div>

          <div class="dialog__body">

            <vue-plyr
              class="fit"
              ref="plyr"
              v-if="source.provider=='youtube'"
            >
              <div
                :data-plyr-embed-id="source.file"
                data-plyr-provider="youtube"
              ></div>
            </vue-plyr>
            <vue-plyr
              ref="plyr"
              v-if="source.provider=='video'"
            >
              <video
                controls
                crossorigin
                playsinline
              >
                <source
                  :src="source.file"
                  type="video/mp4"
                />
              </video>
            </vue-plyr>

          </div>

        </div>


        <div
          class="player-audio flex"
          v-if="source.format === 'audio'"
        >

          <div
            @mouseout="onHoverChange(false)"
            @mouseover="onHoverChange(true)"
            class="player-audio__player"
          >
            <div class="flex no-wrap">

              <div style="width: calc(100% - 48px)">

                <div class="player-audio__desc q-mb-xs s-font-sm">

                  <marquee-text>
                    <router-link
                      :to="itemUrl"
                      class="s-link text-white q-mr-xl"
                    >
                      {{itemTitle}}
                    </router-link>
                  </marquee-text>

                </div>

                <vue-plyr
                  :options="playerOptionsAudio"
                  ref="plyr"
                  v-if="source.provider=='audio'"
                >
                  <audio controls crossorigin playsinline>
                    <source
                      :src="source.file"
                      type="audio/mp3"
                    />
                  </audio>
                </vue-plyr>

              </div>

              <q-btn
                @click="visible = false"
                class="player-audio__closer q-py-auto"
                dense
                flat
                icon="close"
                size="20px"
              />

            </div>

          </div>

          <q-btn
            :icon="state.playing ? 'pause' : 'play_arrow'"
            @click="visible = false"
            @mouseout="onHoverChange(false)"
            @mouseover="onHoverChange(true)"
            class="player-audio__toggle "
            color="primary"
            dense
            round
            size="23px"
          />

        </div>

      </div>


    </div>

  </q-dialog>

</template>

<script>
  import CDialog from '@common/dialog/component/dialog'
  import Balloon from './balloon'
  import MarqueeText from '~module/app/component/ui/MarqueeText'

  export default {
    extends: CDialog,
    components: {Balloon, MarqueeText},
    props: {
      mode: {},
      itemTitle: {},
      itemUrl: {},
      sourceComId: {},
      source: {},
      time: {},
      playing: {default: false},
      muted: {default: false},
      maximized: {default: false},
      volume: {},
      onStart: {default: null},
      onReady: {default: null},
    },
    data() {
      return {
        dialogModule: 'app',
        dialogName: 'player',
        reset: true,
        hover: false,
        hoverTimeout: null,
        visibleProxy: this.$store.getters['dialogVisible']('app/player')
      }
    },
    created() {
      this.state.maximized = this.maximized
      this.state.uid = this.source.file;

      console.log({itemTitle: this.itemTitle, source: this.source})
    },
    computed: {

      maximizable() {
        return this.source.format === 'video'
      },

      bindDialog() {

        const res = {
          style: {},
          class: {}
        }

        if (this.source.format == 'audio') {
          res.class['theme-white'] = true

        } else {
          res.class['theme-black'] = true

          if (this.isDesktop) {
            res.style.width = this.state.maximized ? '90%' : '400px'
          } else {
            res.style.width = this.state.maximized ? '90%' : '200px'
          }

          res.style.maxWidth = this.state.maximized ? '1000px' : '400px'
        }

        this.state.maximized ? res.class['size-miximized'] = true : res.class['size-minimized'] = true

        return res
      },

      playerOptionsAudio() {
        return {
          controls: ['play-large',  'progress', 'captions', 'pip', 'airplay', 'fullscreen']
        }
      },

      player() {
        return this.$refs.plyr ? this.$refs.plyr.player : null
      },

    },
    watch: {

      'state.playing'(val) {
        const p = this.$refs.plyr && this.$refs.plyr.player
        if (p)
          val ? p.play() : p.pause()
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
      }
    },
    methods: {
      onHoverChange(val) {

        if (this.hoverTimeout) clearTimeout(this.hoverTimeout)

        this.hoverTimeout = setTimeout(() => {
          this.hover = val
        }, 100)
      },

      onVisibleChange(val) {
        this.visible = val
      },

      async init() {

        return new Promise(async (resolve, reject) => {

          const source = this.source;

          if (this.$refs.plyr && this.$refs.plyr.player) this.$refs.plyr.player.destroy()

          this.reset = true;

          setTimeout(() => {

            this.reset = false;

            let playingStart = false

            this.$nextTick(() => {

              if (!this.$refs.plyr) return;

              let p = this.$refs.plyr.player

              p.on('play', async (event) => {
                this.state.playing = true
              })

              p.on('pause', async (event) => {
                this.state.playing = false
              })

              p.on('playing', async (event) => {

                this.state.playing = true

                if (playingStart) return;

                setTimeout(async () => {

                  if (this.onStart) {
                    await this.onStart(this, p);
                  }

                  this.$nextTick(() => {
                    p.muted = this.muted
                  })

                }, 100)

                setTimeout(async () => {
                  p.volume = this.volume + 0.01
                  p.volume = this.volume
                }, 500)

                playingStart = true
              })

              p.on('ready', (event) => {

                setTimeout(async () => {

                  if (this.time)
                    p.currentTime = this.time

                  if (this.volume)
                    p.volume = this.volume

                  if (this.playing) {
                    p.play();
                    p.muted = true
                  }

                  if (this.onReady) {
                    await this.onReady();
                  }

                  resolve();

                }, 50);

              });
            })

          }, 50)
        });

      }
    },
    mounted() {
      this.init()
    },
  }

</script>

<style lang="scss">

  .com-dialog-player {

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
        border: c1px solid #CCC;
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


</style>
