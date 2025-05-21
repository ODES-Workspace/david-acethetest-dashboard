<script lang="ts" setup>

//@ts-ignore
import Swal from 'sweetalert2';
import {onMounted, ref} from "vue";
import axios from "axios";
import moment from "moment-timezone";

const examDate = ref<number>(0);

onMounted(async () => {
  //@ts-ignore
  const response = await axios.postForm(acethetest_dashboard_script.ajax_url, {
    action: 'attd_get_exam_date'
  });
  examDate.value = response.data.data ?? false;
})

async function handleExamDateEdit() {
  const {value: date} = await Swal.fire({
    title: "Select Exam Date",
    input: "date",
    showCloseButton: true,
    showCancelButton: true,
    didOpen: () => {
      const today = (new Date()).toISOString();
      Swal.getInput().min = today.split("T")[0];
    }
  });

  if (date) {
    const timestamp = moment(date, 'YYYY-MM-DD').unix();
    examDate.value = timestamp;
    //@ts-ignore
    await axios.postForm(acethetest_dashboard_script.ajax_url, {
      action: 'attd_set_exam_date',
      date: timestamp
    });

  }
}

</script>

<template>
  <div class="bg-white rounded shadow-md p-2">
    <div class="flex text-sm justify-between font-bold">
      <span>Test Scores</span>
      <span class="dashicons dashicons-analytics"></span>
    </div>
    <div class="flex gap-2">
      <div class="font-bold text-xl">0</div>
      <span class="dashicons dashicons-edit text-white bg-blue-400 rounded-full cursor-pointer"
            @click="handleExamDateEdit"></span>
    </div>
  </div>
</template>

<style scoped>

</style>