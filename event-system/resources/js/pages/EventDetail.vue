<template>
  <div class="max-w-2xl mx-auto mt-10 bg-white p-6 rounded-lg shadow space-y-6">
    <!-- Back Button -->
    <button
      @click="router.push('/events')"
      class="flex items-center text-blue-600 hover:text-blue-800 transition"
    >
      ← Back to Events
    </button>

    <!-- ✅ Admin Edit/Delete controls at the top -->
    <div v-if="user?.role === 'admin'" class="flex justify-end gap-3 mb-4">
      <button
        @click="editEvent"
        class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 transition"
      >
        ✏️ Edit
      </button>
      <button
        @click="deleteEvent"
        :disabled="deleting"
        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition disabled:opacity-60"
      >
        {{ deleting ? "Deleting..." : "🗑️ Delete" }}
      </button>
    </div>

    <!-- Event Info -->
    <div>
      <h2 class="text-3xl font-semibold mb-2">{{ event.title }}</h2>
      <p class="text-gray-600 mb-4">{{ event.description }}</p>

      <div class="text-sm text-gray-500 mb-6 space-y-1">
        <p>📅 <strong>Date:</strong> {{ formattedDate }}</p>
        <p>📍 <strong>Location:</strong> {{ event.location }}</p>
        <p>🎟️ <strong>Capacity:</strong> {{ event.capacity }}</p>
      </div>
    </div>

    <!-- Seat Management or RSVP -->
    <div class="flex justify-center items-center">
      <!-- User RSVP -->
      <div v-if="!user?.role || user?.role === 'user'" class="space-y-4 w-full text-center">
        <button
          v-if="!showSeatPicker"
          @click="showSeatPicker = true"
          class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition"
        >
          🎟️ RSVP Now
        </button>

        <!-- Seat Picker for users -->
        <SeatPicker
          v-if="showSeatPicker"
          :eventId="event.id"
          @seat-selected="handleSeatSelected"
          @cancel="showSeatPicker = false"
        />
      </div>

      <!-- Admin seat management -->
      <div v-if="user?.role === 'admin'" class="space-y-4 w-full text-center">
        <button
          v-if="!showSeatPicker"
          @click="showSeatPicker = true"
          class="bg-yellow-500 text-white px-6 py-2 rounded hover:bg-yellow-600 transition"
        >
          🪑 Manage Seats
        </button>

        <SeatPicker
          v-if="showSeatPicker"
          :eventId="event.id"
          :isAdmin="true"
          @cancel="showSeatPicker = false"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";
import SeatPicker from "../components/SeatPicker.vue";

const route = useRoute();
const router = useRouter();
const auth = useAuthStore();
const showSeatPicker = ref(false);

const event = ref({});
const deleting = ref(false);
const user = computed(() => auth.user);

onMounted(async () => {
  const { data } = await axios.get(`http://127.0.0.1:8000/api/events/${route.params.id}`);
  event.value = data;
});

const formattedDate = computed(() => {
  if (!event.value.date) return "";
  const d = new Date(event.value.date);
  return d.toLocaleDateString("en-US", {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  });
});

function editEvent() {
  router.push(`/events/${event.value.id}/edit`);
}

async function deleteEvent() {
  if (!confirm("Are you sure you want to delete this event?")) return;
  deleting.value = true;
  try {
    await axios.delete(`http://127.0.0.1:8000/api/events/${event.value.id}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    });
    alert("Event deleted successfully.");
    router.push("/events");
  } catch (error) {
    console.error(error);
    alert(error.response?.data?.message || "Failed to delete event.");
  } finally {
    deleting.value = false;
  }
}

async function handleSeatSelected(seat) {
  try {
    const token = localStorage.getItem("token");
    if (!token) {
      alert("Please log in to RSVP.");
      router.push("/login");
      return;
    }

    await axios.post(
      `http://127.0.0.1:8000/api/events/${event.value.id}/rsvp`,
      { seat_id: seat.id },
      { headers: { Authorization: `Bearer ${token}` } }
    );

    alert(`RSVP confirmed for seat ${seat.label}!`);
    showSeatPicker.value = false;
  } catch (error) {
    console.error(error);
    alert(error.response?.data?.message || "Failed to RSVP.");
  }
}
</script>
