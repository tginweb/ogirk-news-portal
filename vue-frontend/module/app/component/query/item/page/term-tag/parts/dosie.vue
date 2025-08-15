<template>
  <div>

    <a :href="data.url" target="_blank">

      <div class="header flex no-wrap q-mb-md">

        <div class="header__caption q-mr-sm text-grey-8">Статья энциклопедии</div>

        <div class="q-ml-auto">

          <img class="header__logo" src="http://irkipedia.ru/sites/all/themes/EDU/css/images/irki3.png"/>

        </div>

      </div>

      <div class="header__title q-mb-md text-grey-9 s-font-lg">
        {{data.title}}
      </div>

    </a>

    <template v-if="screen.gt.sm">

      <div class="gallery q-mb-md" v-if="data.gallery && data.gallery.length">

        <q-tabs
          align="left"
          class=""
          inline-label
          v-model="galleryTab"
        >
          <q-tab
            :key="index"
            :name="index"
            class="q-pa-none q-mr-xs"
            content-class="q-pt-none"
            v-for="(galleryItem, index) of data.gallery"
          >
            <img
              :src="galleryItem.src"
              class="gallery__image"
            />
          </q-tab>

        </q-tabs>

      </div>

      <a :href="data.url" target="_blank">
        <div class="teaser text-grey-9 s-font-sm s-font-md-md" v-html="teaser"></div>
      </a>

    </template>

    <a :href="data.url" class="link q-pa-sm text-center q-mt-md" target="_blank">
      перейти к статье
    </a>

  </div>
</template>

<script>

  export default {
    props: {
      item: {},
      data: {}
    },
    data() {
      return {
        galleryTab: null
      }
    },
    computed: {
      teaser() {
        return this.data.teaser + '...'
      }
    }
  }
</script>

<style lang="scss" scoped>

  .header {
    font-weight1: bold;
  }

  .header__logo {
    max-width: 130px;
    height: auto;
  }

  .header__title {
    font-weight: bold;
    line-height: normal;
  }

  .teaser {

  }

  .gallery {

  }

  .gallery__image {
    width: 64px;
    height: auto;
  }

  .link {
    display: block;
    text-decoration: none;
    border: 1px solid #620000;
    color: #620000;
  }

  a {
    text-decoration: none;
  }

</style>
