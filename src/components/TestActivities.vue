<template>
  <div class="min-h-screen bg-gray-50 p-8">
    <div class="max-w-4xl mx-auto">
      <div class="bg-white rounded-lg shadow-sm p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Test Activities</h1>

        <!-- Scrollable container -->
        <div class="h-96 overflow-y-auto pr-2">
          <div class="space-y-4">
            <div
                v-for="(course, courseIndex) in testActivities"
                :key="courseIndex"
                class="border-b border-gray-200 pb-4 last:border-b-0"
            >
              <!-- Course Title -->
              <h2 class="text-xl font-bold text-gray-900 mb-3">{{ course.courseTitle }}</h2>

              <!-- Quizzes for this course -->
              <div class="space-y-2 ml-4">
                <div
                    v-for="(quiz, quizIndex) in course.quizzes"
                    :key="quizIndex"
                    class="border-l-2 border-gray-100 pl-4 pb-2"
                >
                  <!-- Quiz Name -->
                  <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ quiz.name }}</h3>

                  <!-- Attempts for this quiz -->
                  <div class="space-y-1">
                    <div
                        v-for="(attempt, attemptIndex) in quiz.attempts"
                        :key="attemptIndex"
                        class="ml-2"
                    >
                      <div class="flex justify-between items-start mb-1">
                        <div>
                          <p class="text-sm text-gray-600">Attempt {{ attemptIndex + 1 }}: {{ attempt.score }}/{{ attempt.questions }}</p>
                        </div>
                        <span class="text-lg font-bold text-gray-900">{{ attempt.percentage }}%</span>
                      </div>

                      <div class="mb-1">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                          <div
                              class="h-2 rounded-full transition-all duration-300"
                              :class="getProgressBarColor(attempt.percentage)"
                              :style="{ width: attempt.percentage + '%' }"
                          ></div>
                        </div>
                      </div>

                      <p class="text-xs text-gray-500 mb-2">{{ attempt.date }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const testActivities = ref([
  {
    courseTitle: "Life & Health Insurance",
    quizzes: [
      {
        name: "Module 1 Quiz: Insurance Basics",
        attempts: [
          {
            score: 6,
            questions: 10,
            percentage: 60,
            date: "May 10, 2025"
          },
          {
            score: 8,
            questions: 10,
            percentage: 80,
            date: "May 11, 2025"
          }
        ]
      },
      {
        name: "Module 3 Quiz: Policy Types",
        attempts: [
          {
            score: 9,
            questions: 10,
            percentage: 90,
            date: "May 12, 2025"
          }
        ]
      },
      {
        name: "Midterm Exam",
        attempts: [
          {
            score: 15,
            questions: 20,
            percentage: 75,
            date: "May 14, 2025"
          },
          {
            score: 17,
            questions: 20,
            percentage: 85,
            date: "May 16, 2025"
          }
        ]
      }
    ]
  },
  {
    courseTitle: "Property & Casualty Insurance",
    quizzes: [
      {
        name: "Module 2 Quiz: Risk Assessment",
        attempts: [
          {
            score: 7,
            questions: 10,
            percentage: 70,
            date: "May 8, 2025"
          },
          {
            score: 8,
            questions: 10,
            percentage: 80,
            date: "May 9, 2025"
          },
          {
            score: 9,
            questions: 10,
            percentage: 90,
            date: "May 10, 2025"
          }
        ]
      },
      {
        name: "Module 4 Quiz: Claims Process",
        attempts: [
          {
            score: 19,
            questions: 20,
            percentage: 95,
            date: "May 11, 2025"
          }
        ]
      }
    ]
  },
  {
    courseTitle: "Insurance Ethics & Compliance",
    quizzes: [
      {
        name: "Ethics Quiz: Professional Standards",
        attempts: [
          {
            score: 10,
            questions: 10,
            percentage: 100,
            date: "May 5, 2025"
          }
        ]
      },
      {
        name: "Compliance Test: Regulations",
        attempts: [
          {
            score: 12,
            questions: 15,
            percentage: 80,
            date: "May 6, 2025"
          },
          {
            score: 14,
            questions: 15,
            percentage: 93,
            date: "May 7, 2025"
          }
        ]
      }
    ]
  }
])

const getProgressBarColor = (percentage) => {
  if (percentage >= 90) return 'bg-green-500'
  if (percentage >= 80) return 'bg-blue-500'
  if (percentage >= 70) return 'bg-yellow-500'
  return 'bg-red-500'
}
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