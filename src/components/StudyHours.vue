<script lang="ts" setup>
//@ts-ignore
import Swal from 'sweetalert2';
import {computed, onMounted, ref} from "vue";
import axios from "axios";
import {type InterfaceManualHours} from '@/Interfaces/InterfaceManualHours';
import {type IBooking} from '@/Interfaces/IBooking';
import SkeletonLoader from "@/components/partials/SkeletonLoader.vue";

const studyHours = ref<{ manual: InterfaceManualHours[], study_hours: IBooking[] }>({manual: [], study_hours: []});
const isLoading = ref(true);
const showTooltip = ref(false);

onMounted(async () => {
  try {
    //@ts-ignore
    const response = await axios.postForm(acethetest_dashboard_script.ajax_url, {
      action: 'attd_get_study_hours'
    });
    studyHours.value = response.data.data ?? false;
  } catch (error) {
    console.error('Error fetching study hours:', error);
  } finally {
    isLoading.value = false;
  }
})

const totalStudyHours = computed(() => {
  const manualHours = studyHours.value.manual.reduce((sum: number, entry: InterfaceManualHours) => {
    const hours = parseFloat(entry.hours);
    return sum + (isNaN(hours) ? 0 : hours);
  }, 0);

  const bookingHours = studyHours.value.study_hours.reduce((sum: number, booking: IBooking) => {
    const minutes = parseFloat(booking.duration);
    const hours = isNaN(minutes) ? 0 : minutes / 60;
    return sum + hours;
  }, 0);

  return (manualHours + bookingHours).toFixed(2);
});

const manualHoursTotal = computed(() => {
  return studyHours.value.manual.reduce((sum: number, entry: InterfaceManualHours) => {
    const hours = parseFloat(entry.hours);
    return sum + (isNaN(hours) ? 0 : hours);
  }, 0).toFixed(2);
});

const bookingHoursTotal = computed(() => {
  const totalMinutes = studyHours.value.study_hours.reduce((sum: number, booking: IBooking) => {
    const minutes = parseFloat(booking.duration);
    return sum + (isNaN(minutes) ? 0 : minutes);
  }, 0);
  return (totalMinutes / 60).toFixed(2);
});

const showStudyHoursPrompt = async () => {
  const formatDateForInput = (usDate: string): string => {
    const [month, day, year] = usDate.split('/');
    if (!month || !day || !year) return new Date().toISOString().split('T')[0];
    return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
  };

  const manualHours = [...studyHours.value.manual].map(entry => ({
    hours: entry.hours ?? '',
    date: entry.date ? formatDateForInput(entry.date) : new Date().toISOString().split('T')[0]
  }));

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

      // Hours Input
      const hoursInput = document.createElement('input');
      hoursInput.type = 'number';
      hoursInput.step = '0.1';
      hoursInput.min = '0';
      hoursInput.placeholder = 'Hours';
      hoursInput.value = val.hours ?? '';
      hoursInput.className = 'input input-bordered w-32 border p-2 rounded';
      hoursInput.addEventListener('input', (e: any) => {
        manualHours[index].hours = e.target.value;
      });

      // Date Input
      const dateInput = document.createElement('input');
      dateInput.type = 'date';
      dateInput.placeholder = 'Date';
      dateInput.value = val.date ?? '';
      dateInput.className = 'input input-bordered w-40 border p-2 rounded';
      dateInput.addEventListener('input', (e: any) => {
        manualHours[index].date = e.target.value;
      });

      // Remove Button
      const removeButton = document.createElement('button');
      removeButton.className = 'bg-red-500 text-white px-2 py-1 rounded';
      removeButton.innerText = 'Remove';
      removeButton.addEventListener('click', () => {
        manualHours.splice(index, 1);
        renderFields();
      });

      fieldWrapper.appendChild(hoursInput);
      fieldWrapper.appendChild(dateInput);
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
    manualHours.push({hours: '0', date: new Date().toISOString().split('T')[0]});
    renderFields();
  });

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

    // Optional: validate each entry here before sending

    // @ts-ignore
    await axios.postForm(acethetest_dashboard_script.ajax_url, {
      action: 'attd_set_study_hours',
      manual_hours: result.value
    });
  }
};
</script>

<template>
  <div class="bg-white rounded shadow-md p-2 relative">
    <!-- Tooltip -->
    <div
        v-if="showTooltip"
        class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 z-10 bg-gray-800 text-white text-xs rounded-lg px-3 py-2 shadow-lg whitespace-nowrap max-w-xs"
    >
      <div class="space-y-1">
        <div class="flex justify-between gap-4">
          <span>Manual:</span>
          <span class="font-semibold">{{ manualHoursTotal }}</span>
        </div>
        <div class="flex justify-between gap-4">
          <span>Classroom Class:</span>
          <span class="font-semibold">{{ bookingHoursTotal }}</span>
        </div>
        <div class="flex justify-between gap-4">
          <span>On Demand Class:</span>
          <span class="font-semibold">0</span>
        </div>
        <div class="border-t border-gray-600 pt-1 mt-1">
          <div class="flex justify-between gap-4">
            <span>Total:</span>
            <span class="font-bold">{{ totalStudyHours }}</span>
          </div>
        </div>
      </div>
      <!-- Tooltip Arrow -->
      <div
          class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-800"></div>
    </div>

    <!-- Loading Skeleton -->
    <skeleton-loader v-if="isLoading"/>

    <!-- Actual Content -->
    <div
        v-else
        @mouseenter="showTooltip = true"
        @mouseleave="showTooltip = false"
    >
      <div class="flex text-sm justify-between font-bold">
        <span>Study Hours</span>
        <span class="dashicons dashicons-book"></span>
      </div>
      <div class="flex gap-2 items-center">
        <div class="font-bold text-xl">
          {{ totalStudyHours }}
        </div>
        <span
            class="dashicons dashicons-edit text-white bg-blue-400 rounded-full cursor-pointer hover:bg-blue-500 transition-colors"
            @click="showStudyHoursPrompt"
        ></span>
      </div>
    </div>
  </div>
</template>

<style scoped>
</style>