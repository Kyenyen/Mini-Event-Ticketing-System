<template>
  <div class="max-w-6xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-md text-blue-600">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-semibold">Upcoming Events</h2>

      <!-- Admin-only Add Event button -->
      <button
        v-if="user?.role === 'admin'"
        @click="router.push('/events/add-event')"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
      >
        + Add Event
      </button>
    </div>

    <!-- Event list -->
    <div v-if="events.length" class="grid md:grid-cols-2 gap-6">
      <div
        v-for="event in events"
        :key="event.id"
        class="border rounded-lg p-4 shadow-sm hover:shadow-md transition cursor-pointer bg-white"
        @click="router.push(`/events/${event.id}`)"
      >
        <h3 class="text-lg font-semibold mb-2 text-blue-600">{{ event.title }}</h3>
        <p class=" mb-3 line-clamp-2 text-blue-600">{{ event.description }}</p>

  <div class="text-sm text-gray-500 space-y-1">
          <p>
            📅 <strong>Date:</strong> {{ formatDate(event.date) }}
          </p>
          <p>
            📍 <strong>Location:</strong> {{ event.location }}
          </p>
          <p>
            🎟️ <strong>Capacity:</strong> {{ event.capacity }}
          </p>
        </div>
          <div class="flex justify-between items-center">
          <router-link
            :to="`/events/${event.id}`"
            class="text-blue-400 hover:underline"
          >
            View Details
          </router-link>
        </div>
      </div>
    </div>

    <div v-else class="text-center text-gray-400 py-10">
      No events available.
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import axios from '@/axios'
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";

const events = ref([]);
const router = useRouter();
const auth = useAuthStore();
const user = computed(() => auth.user);

onMounted(async () => {
  const { data } = await axios.get('/api/events');
  events.value = data;
});

function formatDate(dateStr) {
  if (!dateStr) return "";
  const d = new Date(dateStr);
  return d.toLocaleDateString("en-US", {
    weekday: "long", // 👈 Adds the day
    year: "numeric",
    month: "long",
    day: "numeric",
  });
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
