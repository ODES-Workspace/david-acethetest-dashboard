<template>
  <div class="flex gap-8 flex-wrap">
    <exam-date class="flex-1"/>
    <study-hours class="flex-1"/>
    <test-scores class="flex-1"/>
  </div>
</template>

<script lang="ts" setup>
import ExamDate from "@/components/ExamDate.vue";
import StudyHours from "@/components/StudyHours.vue";
import TestScores from "@/components/TestScores.vue";
</script>
<style scoped>
</style>
