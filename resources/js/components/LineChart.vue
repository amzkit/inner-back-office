<template>
  <Line :data="chartdata" :options="options" />
</template>

<script lang="ts">

import 'chart.js/auto';
import { Line } from 'vue-chartjs'

export default {
  name: 'LineChart',
  components: { Line },
  props: {
    chartdata: {
      type: Object,
      default: null
    },
    options: {
      type: Object,
      default: null
    }
  },
}

</script>

<style>

</style>
