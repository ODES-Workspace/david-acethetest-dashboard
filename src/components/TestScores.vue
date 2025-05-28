<script lang="ts" setup>
//@ts-ignore
import {computed, onMounted, ref} from "vue";
import axios from "axios";
import type {ICourseAverage} from "@/Interfaces/ICourseAvgerage.ts";

const testScores = ref<ICourseAverage[]>([]);
const isLoading = ref(true);
const showTooltip = ref(false);

onMounted(async () => {
  try {
    //@ts-ignore
    const response = await axios.postForm(acethetest_dashboard_script.ajax_url, {
      action: 'attd_get_test_scores'
    });
    testScores.value = response.data.data ?? [];
  } catch (error) {
    console.error('Error fetching test scores:', error);
  } finally {
    isLoading.value = false;
  }
})

const averageScore = computed(() => {
  if (testScores.value.length === 0) return '0.0';

  const total = testScores.value.reduce((sum, course) => {
    return sum + (course.average_score || 0);
  }, 0);

  return (total / testScores.value.length).toFixed(1);
});

const openCourse = (url: string) => {
  window.open(url, '_blank');
};
</script>

<template>
  <div
      class="bg-white rounded shadow-md p-2 relative"
      @mouseenter="showTooltip = true"
      @mouseleave="showTooltip = false"
  >
    <!-- Tooltip -->
    <div
        v-if="showTooltip && !isLoading && testScores.length > 0"
        class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 z-10 bg-gray-800 text-white text-xs rounded-lg px-3 py-2 shadow-lg whitespace-nowrap max-w-xs"
    >
      <div class="space-y-1 max-h-48 overflow-y-auto">
        <div
            v-for="course in testScores"
            :key="course.course_id"
            class="flex justify-between gap-4 cursor-pointer hover:bg-gray-700 px-1 py-0.5 rounded transition-colors"
            @click="openCourse(course.course_url)"
        >
          <span class="truncate">{{ course.course_title }}:</span>
          <span class="font-semibold flex-shrink-0"
                :class="course.average_score >= 80 ? 'text-green-400' :
                       course.average_score >= 60 ? 'text-yellow-400' :
                       'text-red-400'">
            {{ course.average_score?.toFixed(1) || '0.0' }}%
          </span>
        </div>
        <div v-if="testScores.length > 1" class="border-t border-gray-600 pt-1 mt-1">
          <div class="flex justify-between gap-4">
            <span>Average:</span>
            <span class="font-bold"
                  :class="parseFloat(averageScore) >= 80 ? 'text-green-400' :
                         parseFloat(averageScore) >= 60 ? 'text-yellow-400' :
                         'text-red-400'">
              {{ averageScore }}%
            </span>
          </div>
        </div>
      </div>
      <!-- Tooltip Arrow -->
      <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-b-4 border-transparent border-b-gray-800"></div>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="isLoading" class="animate-pulse">
      <div class="flex text-sm justify-between mb-2">
        <div class="h-4 bg-gray-200 rounded w-20"></div>
        <div class="h-4 w-4 bg-gray-200 rounded"></div>
      </div>
      <div class="flex gap-2">
        <div class="h-7 bg-gray-200 rounded w-12"></div>
      </div>
    </div>

    <!-- Actual Content -->
    <div v-else>
      <div class="flex text-sm justify-between font-bold">
        <span>Test Scores</span>
        <span class="dashicons dashicons-analytics"></span>
      </div>
      <div class="flex gap-2 items-center">
        <div class="font-bold text-xl"
             :class="parseFloat(averageScore) >= 80 ? 'text-green-600' :
                    parseFloat(averageScore) >= 60 ? 'text-yellow-600' :
                    'text-red-600'">
          {{ averageScore }}%
        </div>
        <div v-if="testScores.length > 0" class="text-xs text-gray-500">
          ({{ testScores.length }} course{{ testScores.length !== 1 ? 's' : '' }})
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
</style>