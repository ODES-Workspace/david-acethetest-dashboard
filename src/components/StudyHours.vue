<script lang="ts" setup>

//@ts-ignore
import Swal from 'sweetalert2';
import {onMounted, ref} from "vue";
import axios from "axios";

const studyHours = ref<{ manual: string[] }>({manual: []});

onMounted(async () => {
  //@ts-ignore
  const response = await axios.postForm(acethetest_dashboard_script.ajax_url, {
    action: 'attd_get_study_hours'
  });
  studyHours.value = response.data.data ?? false;
})


const showStudyHoursPrompt = async () => {
  const manualHours = [...studyHours.value.manual];
  const container = document.createElement('div');
  container.className = 'space-y-4';
  container.id = 'acethetest-dashboard';

  const fieldsWrapper = document.createElement('div');
  fieldsWrapper.className = 'space-y-2';

  const renderFields = () => {
    fieldsWrapper.innerHTML = '';
    manualHours.forEach((val, index) => {
      const fieldWrapper = document.createElement('div');
      fieldWrapper.className = 'flex items-center gap-2';

      const input = document.createElement('input');
      input.type = 'number';
      input.value = val;
      input.className = 'input input-bordered w-full max-w-xs border p-2 rounded';
      input.addEventListener('input', (e: any) => {
        manualHours[index] = e.target.value;
      });

      const removeButton = document.createElement('button');
      removeButton.className = 'bg-red-500 text-white px-2 py-1 rounded';
      removeButton.innerText = 'Remove';
      removeButton.addEventListener('click', () => {
        manualHours.splice(index, 1);
        renderFields();
      });

      fieldWrapper.appendChild(input);
      if (manualHours.length > 1) {
        fieldWrapper.appendChild(removeButton);
      }

      fieldsWrapper.appendChild(fieldWrapper);
    });
  };

  container.appendChild(fieldsWrapper);
  const addButton = document.createElement('button');
  addButton.className = 'bg-blue-500 border-none mt-1 text-white px-3 py-1 rounded';
  addButton.innerText = 'Add Study Hour';
  addButton.addEventListener('click', () => {
    manualHours.push('0');
    renderFields();
  });
  // Append in order: Add Button, then Input Fields
  container.appendChild(addButton);
  renderFields();

  const result = await Swal.fire({
    title: 'Enter Manual Hours',
    html: container,
    confirmButtonText: 'Submit',
    focusConfirm: false,
    preConfirm: () => [...manualHours]
  });

  if (result.isConfirmed) {
    studyHours.value.manual = result.value;
    // @ts-ignore
    await axios.postForm(acethetest_dashboard_script.ajax_url, {
      action: 'attd_set_study_hours',
      hours: result.value
    });
  }
};

</script>

<template>
  <div class="bg-white rounded shadow-md p-2">
    <div class="flex text-sm justify-between font-bold">
      <span>Study Hours</span>
      <span class="dashicons dashicons-book"></span>
    </div>
    <div class="flex gap-2">
      <div class="font-bold text-xl">{{ studyHours.manual.reduce((sum, hour) => sum + parseInt(hour), 0) }}</div>
      <span class="dashicons dashicons-edit text-white bg-blue-400 rounded-full cursor-pointer"
            @click="showStudyHoursPrompt"></span>
    </div>
  </div>
</template>

<style scoped>

</style>