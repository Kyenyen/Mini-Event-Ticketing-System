<template>
  <div class="max-w-4xl mx-auto mt-10 bg-white p-6 rounded-lg shadow space-y-6">

    <!-- Admin tip -->
    <div v-if="user?.role === 'admin'" class="text-center text-sm text-gray-500 mt-2">
      💡 Click a seat to <b>block</b> it, click again to <b>unblock</b>.
    </div>

    <!-- Stage -->
    <div class="bg-gray-800 text-white py-2 rounded text-center font-semibold tracking-wide">
      🎭 STAGE
    </div>

    <!-- Seat Grid -->
    <div v-if="seats.length" class="grid grid-cols-10 gap-2 justify-center mt-4">
      <button
        v-for="seat in seats"
        :key="seat.id"
        @click="handleSeatClick(seat)"
        :disabled="seat.status === 'booked' || seat.status === 'processing' || (seat.status === 'blocked' && user?.role !== 'admin')"
        :class="seatButtonClass(seat)"
      >
        {{ seat.label }}
      </button>
    </div>

    <div v-else class="text-center text-gray-400 py-10">
      No seats available.
    </div>

    <!-- Legend -->
    <div class="flex justify-center gap-6 text-sm mt-6 text-gray-600">
      <div class="flex items-center gap-1"><span class="w-4 h-4 bg-green-500 rounded"></span> Available</div>
      <div class="flex items-center gap-1"><span class="w-4 h-4 bg-blue-500 rounded"></span> Selected</div>
      <div class="flex items-center gap-1"><span class="w-4 h-4 bg-red-500 rounded"></span> Booked</div>
      <div class="flex items-center gap-1"><span class="w-4 h-4 bg-yellow-400 rounded"></span> Blocked</div>
      <div class="flex items-center gap-1"><span class="w-4 h-4 bg-gray-400 rounded"></span> Processing</div>
    </div>

    <!-- User Action Buttons -->
    <div class="flex justify-center gap-3 mt-8" v-if="user?.role !== 'admin'">
      <button
        @click="confirmRsvp"
        :disabled="!selectedSeat || loading"
        class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition disabled:opacity-50"
      >
        {{ loading ? "Processing..." : "Confirm RSVP" }}
      </button>

      <button
        @click="router.push('/events')"
        class="bg-gray-300 px-6 py-2 rounded hover:bg-gray-400 transition"
      >
        Cancel
      </button>
    </div>

    <!-- Admin Seat Summary -->
    <div v-if="user?.role === 'admin'" class="mt-6 text-center text-gray-700 text-sm">
      🟢 Available: {{ countSeats('available') }} |
      🟡 Blocked: {{ countSeats('blocked') }} |
      🔴 Booked: {{ countSeats('booked') }} |
      ⚙️ Processing: {{ countSeats('processing') }}
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { useRouter, useRoute } from "vue-router";
import { useAuthStore } from "../stores/auth";

const route = useRoute();
const router = useRouter();
const auth = useAuthStore();
const user = computed(() => auth.user);

const event = ref({});
const seats = ref([]);
const selectedSeat = ref(null);
const loading = ref(false);

// ✅ Fetch all seats
async function fetchSeats() {
  try {
    const res = await axios.get(`http://127.0.0.1:8000/api/events/${route.params.id}/seats`);
    seats.value = res.data;
  } catch (err) {
    console.error("Failed to fetch seats:", err);
  }
}

// ✅ Fetch event & seats on mount
onMounted(async () => {
  const eventRes = await axios.get(`http://127.0.0.1:8000/api/events/${route.params.id}`);
  event.value = eventRes.data;
  await fetchSeats();
});

// ✅ Format readable date
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

// ✅ Count seats by status
function countSeats(status) {
  return seats.value.filter((s) => s.status === status).length;
}

// ✅ Dynamic seat colors
function seatButtonClass(seat) {
  const base = "w-10 h-10 rounded flex items-center justify-center font-semibold border transition";
  if (selectedSeat.value?.id === seat.id) return `${base} bg-blue-500 text-white`;
  if (seat.status === "booked") return `${base} bg-red-500 text-white cursor-not-allowed`;
  if (seat.status === "blocked") return `${base} bg-yellow-400 text-black cursor-not-allowed`;
  if (seat.status === "processing") return `${base} bg-gray-400 text-white cursor-wait`;
  if (seat.status === "available") return `${base} bg-green-500 text-white hover:bg-green-600`;
  return `${base} bg-gray-300 text-gray-700`;
}

// ✅ Admin seat toggle
async function handleSeatClick(seat) {
  if (user.value?.role === "admin") {
    try {
      const token = localStorage.getItem("token");
      const action = seat.status === "blocked" ? "unblock" : "block";
      await axios.put(
        `http://127.0.0.1:8000/api/seats/${seat.id}/${action}`,
        {},
        { headers: { Authorization: `Bearer ${token}` } }
      );
      await fetchSeats(); // refresh seat grid
      alert(`Seat ${seat.label} ${action === "block" ? "blocked" : "unblocked"} successfully.`);
    } catch (error) {
      console.error("Failed to update seat:", error);
      alert("Failed to change seat status.");
    }
  } else {
    if (seat.status !== "available") return;
    selectedSeat.value = seat;
  }
}

// ✅ RSVP for normal users
async function confirmRsvp() {
  if (!selectedSeat.value) return alert("Please select a seat first.");
  loading.value = true;
  try {
    const token = localStorage.getItem("token");
    await axios.post(
      `http://127.0.0.1:8000/api/events/${event.value.id}/rsvp`,
      { seat_id: selectedSeat.value.id },
      { headers: { Authorization: `Bearer ${token}` } }
    );
    alert(`🎟️ Seat ${selectedSeat.value.label} confirmed!`);
    await fetchSeats(); // refresh after booking
    router.push("/events");
  } catch (error) {
    console.error(error);
    alert(error.response?.data?.message || "Failed to RSVP.");
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
button {
  transition: all 0.2s ease;
}
</style>
