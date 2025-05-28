<script lang="ts" setup>

//@ts-ignore
import Swal from 'sweetalert2';
import {computed, onMounted, ref} from "vue";
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

const isExamCritical = computed(() => {
  if (!examDate.value) return false;

  const now = moment();
  const exam = moment.unix(examDate.value);

  const diffHours = exam.diff(now, 'hours');

  // If exam date is in past 24 hours or next 24 hours, mark critical
  return diffHours >= -24 && diffHours <= 24;
});
async function handleExamDateEdit() {
  const currentDate = examDate.value
      ? moment.unix(examDate.value).format('YYYY-MM-DD')
      : '';
  const {value: date} = await Swal.fire({
    title: "Select Exam Date",
    input: "date",
    inputValue: currentDate,
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
      <span>Exam Date</span>
      <span class="dashicons dashicons-calendar"></span>
    </div>
    <div class="flex gap-2">
      <div v-if="examDate" :class="['font-bold text-xl', isExamCritical ? 'text-red-500' : '']"
      >{{ moment.unix(examDate).format("MM/DD/YYYY") }}
      </div>
      <div v-else class="text-red-400">Not set</div>
      <span class="dashicons dashicons-edit text-white bg-blue-400 rounded-full cursor-pointer"
            @click="handleExamDateEdit"></span>
    </div>
  </div>
</template>

<style scoped>

</style>