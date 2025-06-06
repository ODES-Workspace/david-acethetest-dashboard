<template>
  <div class="bg-white rounded-lg shadow-md p-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Upcoming Zoom Classes</h1>

    <!-- Scrollable container -->
    <div class="h-96 overflow-y-auto pr-2">
      <!-- Loading Skeleton -->
      <div v-if="isLoading" class="space-y-4">
        <div
            v-for="i in 3"
            :key="i"
            class="border-b border-gray-200 pb-4"
        >
          <div class="flex justify-between items-center mb-3">
            <div class="h-6 bg-gray-200 rounded w-2/3 animate-pulse"></div>
            <div class="flex space-x-2">
              <div class="h-8 bg-gray-200 rounded w-24 animate-pulse"></div>
              <div class="h-8 bg-gray-200 rounded w-20 animate-pulse"></div>
            </div>
          </div>
          <div class="h-4 bg-gray-200 rounded w-1/3 animate-pulse"></div>
        </div>
      </div>

      <!-- Actual Content -->
      <div v-else class="space-y-2">
        <div
            v-for="(booking, index) in zoomClasses"
            :key="booking.id"
            class="border-b border-gray-200 pb-4 last:border-b-0"
        >
          <!-- Service Name and Buttons Row -->
          <div class="flex justify-between items-center mb-0">
            <!-- Service Name -->
            <h2 class="text-xl font-bold text-gray-900 flex-1 mr-4">{{ booking.service.name }}</h2>

            <!-- Buttons Container -->
            <div class="flex space-x-2 flex-shrink-0">
              <!-- Join Class Button -->
              <a :href="booking.zoom_url" target="_blank">
              <button
                  class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
              >
                <Video class="w-4 h-4 mr-2" />
                Join Class
              </button>
              </a>

              <!-- Download Button -->
              <button
                  v-if="booking.service.attachment"
                  @click="downloadAttachment(booking.service.attachment, booking.service.name)"
                  class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
                  :title="`Download ${getFileType(booking.service.attachment)} file`"
              >
                <Download class="w-4 h-4 mr-2" />
                Download
              </button>

              <!-- No attachment placeholder (maintains layout) -->
              <div v-else class="w-20"></div>
            </div>
          </div>

          <!-- Booking Start Date Time -->
          <p class="text-sm text-gray-600 mb-1">
            {{ formatDateTime(booking.start_datetime_utc) }}
          </p>

          <!-- Booking Code and File Type -->
          <div class="flex items-center justify-between">
            <p class="text-xs text-gray-500">
              Booking Code: {{ booking.booking_code }}
            </p>
          </div>
        </div>

        <!-- Empty state -->
        <div v-if="zoomClasses.length === 0" class="text-center py-8">
          <p class="text-gray-500 text-lg">No upcoming zoom classes found.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { Video, Download } from 'lucide-vue-next'

const zoomClasses = ref([])
const isLoading = ref(true)

onMounted(async () => {
  try {
    //@ts-ignore
    const response = await axios.postForm(acethetest_dashboard_script.ajax_url, {
      action: 'attd_get_upcoming_zoom_classes'
    });
    zoomClasses.value = response.data.data ?? [];
  } catch (error) {
    console.error('Error fetching zoom classes:', error);
  } finally {
    isLoading.value = false;
  }
})

// Format datetime for display
const formatDateTime = (dateTimeString: string) => {
  const date = new Date(dateTimeString);
  return date.toLocaleString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: true
  });
}

// Get file type from URL
const getFileType = (url: string) => {
  if (!url) return 'file';

  const extension = url.split('.').pop()?.toLowerCase();

  switch (extension) {
    case 'pdf':
      return 'pdf';
    case 'doc':
    case 'docx':
      return 'word';
    case 'xls':
    case 'xlsx':
      return 'excel';
    case 'ppt':
    case 'pptx':
      return 'powerpoint';
    case 'jpg':
    case 'jpeg':
    case 'png':
    case 'gif':
    case 'webp':
      return 'image';
    case 'mp4':
    case 'avi':
    case 'mov':
    case 'wmv':
      return 'video';
    case 'mp3':
    case 'wav':
    case 'flac':
      return 'audio';
    case 'zip':
    case 'rar':
    case '7z':
      return 'archive';
    case 'txt':
      return 'text';
    default:
      return 'file';
  }
}

// Get appropriate file name for download
const getDownloadFileName = (url: string, serviceName: string) => {
  const urlParts = url.split('/');
  const originalFileName = urlParts[urlParts.length - 1];

  // If URL already has a proper filename, use it
  if (originalFileName && originalFileName.includes('.')) {
    return originalFileName;
  }
  // Otherwise, create a filename based on service name and detected file type
  const cleanServiceName = serviceName.replace(/[^a-z0-9]/gi, '_').toLowerCase();
  // Get the actual extension from URL
  const extension = url.split('.').pop()?.toLowerCase() || 'pdf';

  return `${cleanServiceName}_attachment.${extension}`;
}



// Download attachment function
const downloadAttachment = (attachmentUrl: string, serviceName: string) => {
  // Create a temporary anchor element to trigger download
  const link = document.createElement('a');
  link.href = attachmentUrl;

  // Get appropriate filename
  const filename = getDownloadFileName(attachmentUrl, serviceName);

  link.download = filename;
  link.target = '_blank'; // Open in new tab as fallback

  // Append to body, click, and remove
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);

  // Optional: Show success message
  console.log(`Downloading ${getFileType(attachmentUrl)} file: ${filename}`);
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