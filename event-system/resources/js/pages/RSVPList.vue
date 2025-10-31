<template>
  <div class="p-6 max-w-4xl mx-auto">
    <h2 class="text-2xl font-semibold mb-6">My RSVPs</h2>

    <div v-if="loading" class="text-gray-500">Loading RSVPs...</div>
    <div v-else-if="rsvps.length === 0" class="text-gray-500">
      You haven’t RSVPed to any events yet.
    </div>

    <div v-else>
      <!-- ACTIVE RSVPs -->
      <div v-if="activeRsvps.length > 0">
        <h3 class="text-lg font-semibold mb-3 text-green-600">Active RSVPs</h3>
        <div v-for="rsvp in activeRsvps" :key="rsvp.id" class="border rounded-xl p-4 bg-white shadow-sm mb-4">
          <div class="flex justify-between items-center">
            <div>
              <h3 class="text-lg font-semibold">{{ rsvp.event.title }}</h3>
              <p class="text-gray-600 text-sm">{{ formatDate(rsvp.event.date) }}</p>
              <p class="text-sm mt-1">Seat: {{ rsvp.seat?.label || 'N/A' }}</p>
              <p class="text-sm text-gray-500">Status: {{ rsvp.status }}</p>
            </div>

            <div class="flex flex-col gap-2" v-if="rsvp.status !== 'canceled'">
              <button
                @click="toggleSeats(rsvp)"
                class="px-3 py-1 text-sm rounded bg-blue-500 hover:bg-blue-600 text-white"
              >
                {{ rsvp.showSeats ? 'Hide Seats' : 'View Seats' }}
              </button>

              <button
                @click="cancelRsvp(rsvp.id)"
                class="px-3 py-1 text-sm rounded bg-red-500 hover:bg-red-600 text-white"
              >
                Cancel
              </button>
            </div>
          </div>

          <!-- Seat Grid -->
          <div v-if="rsvp.showSeats" class="mt-4">
            <div class="grid grid-cols-8 gap-2">
              <div
                v-for="seat in rsvp.seats"
                :key="seat.id"
                class="p-2 rounded text-center text-xs cursor-default"
                :class="{
                  'bg-green-500 text-white': seat.status === 'available',
                  'bg-gray-400 text-white': seat.status === 'booked',
                  'bg-yellow-500 text-white': seat.id === rsvp.seat?.id
                }"
              >
                {{ seat.label }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- CANCELED RSVPs -->
      <div v-if="cancelledRsvps.length > 0">
        <h3 class="text-lg font-semibold mt-8 mb-3 text-red-600">Canceled RSVPs</h3>
        <div v-for="rsvp in cancelledRsvps" :key="rsvp.id" class="border rounded-xl p-4 bg-gray-50 shadow-sm mb-4 opacity-75">
          <div>
            <h3 class="text-lg font-semibold line-through">{{ rsvp.event.title }}</h3>
            <p class="text-gray-600 text-sm">{{ formatDate(rsvp.event.date) }}</p>
            <p class="text-sm mt-1">Seat: {{ rsvp.seat?.label || 'N/A' }}</p>
            <p class="text-sm text-gray-500">Status: {{ rsvp.status }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from '@/axios'
import Swal from 'sweetalert2'

const rsvps = ref([])
const loading = ref(true)

const fetchRsvps = async () => {
  try {
    const response = await axios.get('/rsvps')
    rsvps.value = response.data.map(r => ({
      ...r,
      showSeats: false,
      seats: [],
    }))
  } catch (error) {
    console.error('Error fetching RSVPs:', error)
  } finally {
    loading.value = false
  }
}

const toggleSeats = async (rsvp) => {
  if (rsvp.showSeats) {
    rsvp.showSeats = false
    return
  }
  try {
    const res = await axios.get(`/events/${rsvp.event.id}/seats`)
    rsvp.seats = res.data
    rsvp.showSeats = true
  } catch (error) {
    console.error('Error fetching seats:', error)
  }
}

const cancelRsvp = async (id) => {
  const result = await Swal.fire({
    title: 'Cancel RSVP?',
    text: "Are you sure you want to cancel your RSVP?",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
  })

  if (result.isConfirmed) {
    try {
      await axios.delete(`/rsvps/${id}`)
      await fetchRsvps()
      Swal.fire('Canceled!', 'Your RSVP has been canceled.', 'success')
    } catch (error) {
      console.error('Error canceling RSVP:', error)
      Swal.fire('Error', 'Failed to cancel RSVP.', 'error')
    }
  }
}

const formatDate = (dateStr) => new Date(dateStr).toLocaleDateString()

const activeRsvps = computed(() => rsvps.value.filter(r => r.status !== 'canceled'))
const cancelledRsvps = computed(() => rsvps.value.filter(r => r.status === 'canceled'))

onMounted(fetchRsvps)
</script>
