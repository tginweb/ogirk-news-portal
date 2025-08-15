<template>
  <transition name='vb-transition'>

    <div class='vb-wrapper' v-if='!closed'>

      <div class="vb-dialog" v-bind="bindDialog">

        <div class='vb-header' v-if="!headerHide">

          <div class='vb-buttons'>

            <q-btn
              @click='maximizedInternal = true'
              color="white"
              dense
              flat
              icon="crop_square"
              size="md"
              v-if='maximizable && !maximizedInternal'
            >

            </q-btn>

            <q-btn
              @click='maximizedInternal = false'
              color="white"
              dense
              flat
              icon="minimize"
              size="md"
              v-if='maximizable && maximizedInternal'
            />

            <q-btn
              icon="close"
              @click='close'
              color="white"
              dense
              flat
              size="md"
            >

            </q-btn>

          </div>

        </div>

        <div class='vb-content'>
          <div class='vb-content-slot'>
            <slot></slot>
          </div>
        </div>

      </div>

      <transition name='vb-transition'>
        <div
          @click='close'
          class='vb-maximized-overlay'
          v-if='maximizedInternal'
        >
        </div>
      </transition>

    </div>
  </transition>
</template>

<script>
  export default {
    props: {
      // balloon title
      title: {
        default: '',
        type: String
      },

      // position: bottom-right, bottom-left, top-right, or top-left
      position: {
        default: 'bottom-right',
        type: String
      },

      // enable the css transform: scale() effect
      zooming: {
        default: false,
        type: Boolean
      },

      // enable the css transform: scale() effect
      maximized: {
        default: true,
        type: Boolean
      },

      maximizable: {
        default: true,
        type: Boolean
      },

      theme: {
        default: 'dark',
        type: String
      },

      dialogStyle: {
        default: () => ({}),
        type: Object
      },

      headerHide: {
        default: false,
        type: Boolean
      }

    },

    data() {
      return {
        closed: false,
        maximizedInternal: this.maximized
      }
    },

    watch: {
      maximized(val) {
        this.maximizedInternal = val
      },
      maximizedInternal(val) {
        this.$emit('update:maximized', val)
      },
    },

    computed: {

      bindDialog() {
        const res = {
          class: {
            [`vb-theme-${this.theme}`]: true,
            [`vb-${this.position}`]: true,
            'vb-maximized': this.maximizedInternal,
            'vb-zoomed-out': !this.maximizedInternal && this.zooming
          },
          style: this.dialogStyle || {}
        }

        return res
      },

    },

    methods: {
      close() {
        this.closed = true
        this.$emit('close', this)
      },

      open() {
        this.closed = false
        this.$emit('open', this)
      },

    }
  }
</script>
<style lang="scss" scoped>

  $transition-length: 0.25s;
  $transition-length-long: $transition-length * 2;
  $balloon-base-width: 320px;
  $content-ratio: 9/16;
  $minimized-padding: 15px;
  $maximized-padding: 20px;
  $small-screen-width: 320px;
  $small-screen-height: 450px;


  .vb-wrapper {
    transition: opacity $transition-length;
  }

  .vb-dialog {
    border-radius: 2px;
    overflow: auto;

    .vb-header {
      box-sizing: content-box;
      display: flex;

      .vb-buttons {
        margin-left: auto;
        box-sizing: border-box;
      }

    }

    .vb-content {
      position: relative;
      overflow: hidden;
    }

    .vb-content-slot {
      transform-origin: 0% 0%;
      transform: scale(1);
      width: 100%;
      height: 100%;
    }

    &.vb-theme-dark,
    &.vb-theme-light {
      &:not(.vb-maximized) {
        box-shadow: 0px 1px 5px rgba(#444, 0.7);
      }

      &.vb-maximized {
        box-shadow: 0px 0px 0px rgba(black, 0.4);
      }
    }

    &.vb-theme-dark {
      .vb-header {
        color: #FFF;
        background: #000;
      }
    }

    &.vb-theme-light {
      .vb-header {
        color: #222;
        background: #EEE;
      }
    }

    &.vb-theme-transparent {
      overflow: visible;

      .vb-header {
        color: #111;
      }

      .vb-content {
        box-shadow: 0px 1px 5px rgba(#444, 0.7);
      }
    }

    &:not(.vb-maximized) {
      z-index: 10000;
      position: fixed;

      .vb-header {
        i {
          font-size: 20px;
          margin: 0 5px 0 5px;
          padding: 6px;
        }
      }

      &.vb-bottom-right {
        left: calc(100% - #{$minimized-padding});
        top: calc(100% - #{$minimized-padding});
        transform: translate(-100%, -100%);
      }
    }

    &.vb-maximized {
      z-index: 10000;
      position: fixed;
      left: 50%;
      top: $maximized-padding;
      transform: translate(-50%, 0);

      .vb-content {
        width1: calc(100vw - #{2 * $maximized-padding});
        max-width1: 1200px;
        overflow: auto;
      }

      .vb-content-slot {
        transform-origin: 0% 0%;
        transform: scale(1);
        width: 100%;
        height: 100%;
      }

      .vb-header {
        i {
          font-size: 25px;
          margin: 0 5px 0 5px;
          padding: 6px;
        }
      }
    }

    &.vb-zoomed-out {
      .vb-content-slot {
        transform-origin: 0% 0%;
        transform: scale(0.25);
        width: 400%;
        height: 400%;
      }
    }
  }


  .vb-maximized-overlay {
    transition: opacity $transition-length-long;
    position: fixed;
    width: 100vw;
    height: 100vh;
    top: 0;
    left: 0;
    background: black;
    z-index: 9999;
    opacity: 0.7;
  }

  .vb-minimized-overlay {
    transition: opacity $transition-length;
    position: absolute;
    height: 100%;
    width: 100%;
    top: 0;
    left: 0;
    opacity: 0;
    background: black;
    cursor: pointer;
    z-index: -1;

    &:hover {
      opacity: 0.7;
    }

    i {
      color: white;
      font-size: 3em;
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
    }
  }

  .vb-transition-enter,
  .vb-transition-leave-active {
    opacity: 0 !important;
  }

  .vb-front {
    z-index: 10004;
  }

</style>
