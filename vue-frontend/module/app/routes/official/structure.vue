<template>
  <q-page class="q-mt-lg q-mb-xl">

    <CLayout>
      <template v-slot:header>
        <el-page-header :title="pageTitle"/>
      </template>

      <template v-slot:default>

        <img src="http://www.ogirk.ru/wp-content/uploads/2025/01/struct-1.png"/>

      </template>

      <template v-slot:right>

        <CSidebar/>

      </template>
    </CLayout>

  </q-page>
</template>

<script>
  import CLayout from '~module/app/layout/main/page/2cols'
  import generateQueryInfo from "@common/graphql-apollo/lib/generate-query-info";
  import CPage from "~module/app/component/route";
  import CSidebar from "./sidebar";
  import CBottom from "./bottom";

  export default {
    extends: CPage,
    components: {
      CLayout,
      CSidebar,
      CBottom
    },
    apollo: {

    },
    data() {
      return {
        page: {
          title: 'Организационная структура ОГАУ «Медиа-центр «Приангарье»'
        },
      }
    },
    computed: {},

  }
</script>
<style lang="scss" scoped>

  .c-item {
    /deep/ .i-wrap {
      padding: 10px 20px;
    }
  }

</style>
