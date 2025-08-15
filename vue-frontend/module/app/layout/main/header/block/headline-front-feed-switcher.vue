<template>

  <div class="com text-center text-md-right">

    <q-tabs
      active-bg-color="blue-grey-3"
      active-color="white"
      indicator-color="transparent"
      class="tabs q-btn-group q-btn-group--rounded q-btn-group--unelevated1"
      dense
    >
      <q-route-tab
        to="/"
        label="Важное"
        exact
      />
      <q-route-tab
        to="/feed/all"
        label="По хронологии"
        exact
      />
    </q-tabs>

  </div>

</template>

<script>

  export default {
    components: {

    },
    apollo: {

    },
    data() {
      return {

      }
    },
    computed: {

    }
  }
</script>

<style lang="scss" scoped>

  .tabs /deep/ {
    .q-tab__label {
      font-size: 12px;
    }
  }

</style>
