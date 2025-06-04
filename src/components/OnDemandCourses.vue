<template>
  <div class="bg-white rounded-lg shadow-md p-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">On Demand Courses</h1>

    <!-- Scrollable container -->
    <div class="h-96 overflow-y-auto pr-2">
      <!-- Loading Skeleton -->
      <div v-if="isLoading" class="space-y-4">
        <div
            v-for="i in 3"
            :key="i"
            class="border-b border-gray-200 pb-4"
        >
          <!-- Course Title Skeleton -->
          <div class="h-6 bg-gray-200 rounded w-1/3 mb-3 animate-pulse"></div>

          <!-- Quiz Skeletons -->
          <div class="space-y-2 ml-4">
            <div
                v-for="j in 2"
                :key="j"
                class="border-l-2 border-gray-100 pl-4 pb-2"
            >
              <!-- Quiz Name Skeleton -->
              <div class="h-5 bg-gray-200 rounded w-2/3 mb-2 animate-pulse"></div>

              <!-- Attempt Skeletons -->
              <div class="space-y-1">
                <div
                    v-for="k in 2"
                    :key="k"
                    class="ml-2"
                >
                  <div class="flex justify-between items-start mb-1">
                    <div class="h-4 bg-gray-200 rounded w-1/4 animate-pulse"></div>
                    <div class="h-4 bg-gray-200 rounded w-12 animate-pulse"></div>
                  </div>
                  <div class="mb-1">
                    <div class="w-full bg-gray-200 rounded-full h-2 animate-pulse"></div>
                  </div>
                  <div class="h-3 bg-gray-200 rounded w-20 mb-2 animate-pulse"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Actual Content -->
      <div v-else class="space-y-4" v-html="courses_html"></div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import {onMounted, ref} from 'vue'
import axios from 'axios'

const courses_html = ref()
const isLoading = ref(true)

onMounted(async () => {
  try {
    //@ts-ignore
    const response = await axios.postForm(acethetest_dashboard_script.ajax_url, {
      action: 'attd_get_on_demand_courses_html'
    });
    courses_html.value = response.data.data ?? '';
  } catch (error) {
    console.error('Error fetching test activities:', error);
  } finally {
    isLoading.value = false;
  }
})

</script>

<style scoped>
/* Custom scrollbar styling */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>