<template>
  <q-page class="q-mt-lg q-mb-xl">

    <CLayout>

      <template v-slot:header>
        <el-page-header :showSubmenu="false" title="Реклама"/>
      </template>

      <template v-slot:default>

        <div class="row q-col-gutter-lg q-mt-lg">

          <div :key="index" class="col-24 col-md-8" v-for="(item, index) of menuItems">

            <router-link :to="item.url" class="c-menu-item block s-link-wrapper q-pa-md q-py-lg text-center text-primary s-font-xl s-font-md-3xl">

                <q-icon :name="item.icon" class="q-mr-md" size="40px"/>

                <span class="">{{item.title}}</span>

            </router-link>

          </div>


        </div>

      </template>

    </CLayout>

  </q-page>
</template>

<script>
  import CPage from "~module/app/component/route";
  import CLayout from '~module/app/layout/main/page/1cols'

  export default {
    name: 'page.advert',
    extends: CPage,
    components: {
      CLayout
    },
    data() {
      return {
        page: {
          title: 'Реклама на сайте'
        },
        menuItems: [
          {title: 'Баннерная реклама', url: '/advert/site/banner', icon: 'far fa-image'},
          {title: 'Тизерная реклама', url: '/advert/site/teaser', icon: 'far fa-sticky-note'},
          {title: 'Реклама в газете', url: '/page/advert/issue', icon: 'far fa-newspaper'},
          {title: 'Услуги пресс-центра', url: '/page/advert/press-center', icon: 'far fa-newspaper'},
        ]
      }
    }
  }
</script>
<style lang="scss" scoped>

  .c-menu-item {
    border: 1px solid #DDD;
    border-radius: 10px;
  }

</style>
