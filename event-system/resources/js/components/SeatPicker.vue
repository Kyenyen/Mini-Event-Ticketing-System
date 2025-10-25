<template>
  <div class="space-y-4">
    <h3 class="text-lg font-semibold text-center">Select Your Seat</h3>

    <div class="grid grid-cols-10 gap-2 justify-center">
      <button
        v-for="seat in seats"
        :key="seat.id"
        :class="seatClass(seat)"
        @click="selectSeat(seat)"
        :disabled="seat.status !== 'available'"
      >
        {{ seat.label }}
      </button>
    </div>

    <div v-if="selectedSeat" class="text-center mt-2">
      Selected Seat: <strong>{{ selectedSeat.label }}</strong>
    </div>

    <div class="text-center mt-4">
      <button
        @click="submitRsvp"
        :disabled="!selectedSeat"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50"
      >
        Confirm RSVP
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
  eventId: Number
})

const seats = ref([])
const selectedSeat = ref(null)

onMounted(async () => {
  const { data } = await axios.get(`http://127.0.0.1:8000/api/events/${props.eventId}/seats`)
  seats.value = data
})

function selectSeat(seat) {
  if (seat.status !== 'available') return
  selectedSeat.value = seat
}

function seatClass(seat) {
  return [
    'w-10 h-10 rounded flex items-center justify-center text-sm font-semibold border transition',
    {
      'bg-green-500 text-white': seat.status === 'available',
      'bg-red-500 text-white': seat.status === 'booked',
      'bg-gray-400 text-white': seat.status === 'blocked',
      'ring-2 ring-blue-500': selectedSeat.value?.id === seat.id
    }
  ]
}

async function submitRsvp() {
  try {
    const token = localStorage.getItem('token')

    // Logged-in users RSVP
    const res = await axios.post(
      `http://127.0.0.1:8000/api/events/${props.eventId}/rsvp`,
      { seat_id: selectedSeat.value.id },
      { headers: token ? { Authorization: `Bearer ${token}` } : {} }
    )

    alert('RSVP confirmed! 🎉')
    console.log(res.data)

    // Optionally mark seat as booked
    selectedSeat.value.status = 'booked'
  } catch (error) {
    console.error(error)
    alert('RSVP failed: ' + (error.response?.data?.message || error.message))
  }
}
</script>
