<script lang="ts" setup>

//@ts-ignore
import {onMounted, ref} from "vue";
import axios from "axios";

const testScores = ref<number>(0);

onMounted(async () => {
  //@ts-ignore
  const response = await axios.postForm(acethetest_dashboard_script.ajax_url, {
    action: 'attd_get_test_scores'
  });
  testScores.value = response.data.data ?? false;
})

</script>

<template>
  <div class="bg-white rounded shadow-md p-2">
    <div class="flex text-sm justify-between font-bold">
      <span>Test Scores</span>
      <span class="dashicons dashicons-analytics"></span>
    </div>
    <div class="flex gap-2">
      <div class="font-bold text-xl">{{ testScores }}</div>
    </div>
  </div>
</template>

<style scoped>

</style>