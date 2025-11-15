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
      <h2 class="text-3xl font-semibold mb-2 text-blue-600">{{ event.title }}</h2>
      <p class="text-blue-600 mb-4">{{ event.description }}</p>

      <div class="text-sm text-gray-500 mb-6 space-y-1">
        <p>📅 <strong>Date:</strong> {{ formattedDate }}</p>
        <p>📍 <strong>Location:</strong> {{ event.location }}</p>
        <p>🎟️ <strong>Capacity:</strong> {{ event.capacity }}</p>
      </div>
    </div>

    <!-- Seat Management or RSVP -->
    <div class="flex justify-center items-center">
  <!-- User or Guest RSVP -->
  <div v-if="user?.role !== 'admin'" class="space-y-4 w-full text-center">
      <button
        v-if="user?.role !== 'admin'"
        @click="handleRsvpClick"
        class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition"
      >
        🎟️ RSVP Now
      </button>

      <!-- Seat picker only for logged-in users -->
      <SeatPicker
        v-if="showSeatPicker && user?.role === 'user'"
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
import Swal from "sweetalert2";

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
  const confirmation = await Swal.fire({
    title: 'Delete event?',
    text: 'Are you sure you want to delete this event? This action cannot be undone.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete',
    cancelButtonText: 'Cancel',
  })
  if (!confirmation.isConfirmed) return

  deleting.value = true;
  Swal.fire({
    title: 'Deleting...',
    html: 'Please wait a moment.',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading(),
  })

  try {
    await axios.delete(`http://127.0.0.1:8000/api/events/${event.value.id}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    });
    Swal.close()
    Swal.fire({
      icon: 'success',
      title: 'Deleted',
      text: 'Event deleted successfully.',
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 1600,
    })
    router.push('/events')
  } catch (error) {
    console.error(error);
    Swal.close()
    Swal.fire({
      icon: 'error',
      title: 'Delete failed',
      text: error.response?.data?.message || 'Failed to delete event.',
    })
  } finally {
    deleting.value = false;
  }
}

async function handleSeatSelected(seat) {
  try {
    const token = localStorage.getItem("token");
    if (!token) {
      const res = await Swal.fire({
        title: 'Not signed in',
        text: 'You must be signed in to reserve a specific seat. Do you want to log in now?',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Go to Login',
        cancelButtonText: 'Cancel',
      })
      if (res.isConfirmed) router.push('/login')
      return
    }

    // Confirm seat booking
    const confirm = await Swal.fire({
      title: `Confirm seat ${seat.label}?`,
      text: 'This seat will be reserved for you.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Yes, confirm',
      cancelButtonText: 'Cancel',
    })
    if (!confirm.isConfirmed) return

    // 🔄 Show progress
    Swal.fire({
      title: 'Booking seat...',
      html: `Reserving seat <strong>${seat.label}</strong>. Please wait.`,
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading(),
    })

    // Send request
    await axios.post(
      `http://127.0.0.1:8000/api/events/${event.value.id}/rsvp`,
      { seat_id: seat.id },
      { headers: { Authorization: `Bearer ${token}` } }
    )

    // ✅ Success feedback
    Swal.fire({
      icon: 'success',
      title: 'Seat Reserved!',
      html: `You have successfully booked seat <strong>${seat.label}</strong>.`,
      timer: 1800,
      showConfirmButton: false,
    })

    showSeatPicker.value = false
  } catch (error) {
    console.error(error)
    Swal.fire({
      icon: 'error',
      title: 'Booking failed',
      text: error.response?.data?.message || 'Failed to RSVP. Please try again.',
    })
  }
}

async function handleRsvpClick() {
  const token = localStorage.getItem("token");

  // ✅ If user logged in → open seat picker
  if (token && user.value) {
    showSeatPicker.value = true;
    return;
  }

  // ✅ Guest flow
  const confirm = await Swal.fire({
    title: "Auto Seat Assignment",
    text: "Your seat will be determined by the system. Are you sure you want to RSVP without an account?",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Yes, continue",
  });

  if (!confirm.isConfirmed) return;

  // Ask for guest name and email
  const { value: formValues } = await Swal.fire({
    title: "Guest RSVP",
    html: `
      <input id="swal-name" class="swal2-input" placeholder="Your Name">
      <input id="swal-email" type="email" class="swal2-input" placeholder="Your Email">
    `,
    focusConfirm: false,
    preConfirm: () => {
      const name = document.getElementById("swal-name").value;
      const email = document.getElementById("swal-email").value;
      if (!name || !email) {
        Swal.showValidationMessage("Please fill in both name and email.");
      }
      return { name, email };
    }
  });

  if (!formValues) return;

  // Submit RSVP to backend
  try {
    const res = await axios.post(
      `http://127.0.0.1:8000/api/events/${event.value.id}/guest-rsvp`,
      formValues
    );

    Swal.fire("Success", `RSVP confirmed! Seat: ${res.data.seat_number}`, "success");
  } catch (err) {
    Swal.fire("Error", err.response?.data?.message || "Failed to RSVP", "error");
  }
}
</script>
