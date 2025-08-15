<template>

  <div class="flex items-center">

    <div class="c-filter q-px-sm " v-if="date">

      фильтр <b>{{date}}</b>

      <q-btn
        dense
        flat
        icon="close"
        :to="url.replace(/\$date/, '')"
        class="q-pa-none"
      />

    </div>

    <q-btn
      color="red-9"
      flat
      :icon="$icons.farCalendarAlt"
      label=""
    >
      <q-menu>
        <CCalendar
          :date="date"
          :filter="filter"
          :url="url"
        />
      </q-menu>
    </q-btn>

  </div>

</template>

<script>

  import CCalendar from './default'

  export default {
    components: {
      CCalendar
    },
    props: {
      date: {},
      filter: {},
      url: {}
    },
  }

</script>

<style lang="scss" scoped>

  .c-filter {
    border-radius: 7px;

    a {
      color: #ad3018;
      text-decoration1: none;
      font-weight1: bold;
    }
  }

</style>
