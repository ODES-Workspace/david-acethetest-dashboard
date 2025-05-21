<template>
  <div class="space-y-2">
    <div v-for="(hour, index) in studyHours" :key="index" class="flex items-center space-x-2">
      <input
          v-model.number="studyHours[index]"
          class="form-input w-full border rounded px-3 py-1"
          min="0"
          placeholder="Enter hours"
          type="number"
      />
      <button
          v-if="studyHours.length > 1"
          class="text-red-500 hover:text-red-700"
          @click="removeField(index)"
      >
        ✕
      </button>
    </div>
    <button
        class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600"
        @click="addField"
    >
      + Add Hour
    </button>

    <!-- Hidden input for preConfirm extraction -->
    <input id="study-hours-json" :value="JSON.stringify(studyHours)" type="hidden"/>
  </div>
</template>

<script setup>
import {ref} from 'vue'

const studyHours = ref([0])

function addField() {
  studyHours.value.push(0)
}

function removeField(index) {
  studyHours.value.splice(index, 1)
}
</script>
