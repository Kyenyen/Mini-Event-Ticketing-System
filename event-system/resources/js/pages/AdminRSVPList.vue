<template>
  <div class="p-6 max-w-6xl mx-auto">
    <h2 class="text-2xl font-semibold mb-6">Manage RSVPs</h2>

    <div v-if="loading" class="text-gray-500">Loading all RSVPs...</div>

    <div v-else>
      <!-- Active RSVPs -->
      <h3 class="text-xl font-semibold mt-4 mb-2">🟢 Active RSVPs</h3>
      <div v-if="activeRsvps.length === 0" class="text-gray-500 mb-6">
        No active RSVPs found.
      </div>

      <div v-else class="space-y-3">
        <div
          v-for="rsvp in activeRsvps"
          :key="rsvp.id"
          class="border rounded-lg p-4 flex justify-between bg-white shadow-sm"
        >
          <div>
            <h4 class="font-semibold text-lg">{{ rsvp.event.title }}</h4>
            <p class="text-sm text-gray-600">
              {{ formatDate(rsvp.event.date) }} | Seat: {{ rsvp.seat.label }}
            </p>
            <p class="text-sm text-gray-500">
              User: {{ rsvp.user.name }} ({{ rsvp.user.email }})
            </p>
          </div>

          <div class="flex flex-col gap-2">
            <button
              @click="cancelRsvp(rsvp.id)"
              class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>

      <!-- Canceled RSVPs -->
      <h3 class="text-xl font-semibold mt-8 mb-2">🔴 Canceled RSVPs</h3>
      <div v-if="canceledRsvps.length === 0" class="text-gray-500">
        No canceled RSVPs.
      </div>

      <div v-else class="space-y-3">
        <div
          v-for="rsvp in canceledRsvps"
          :key="rsvp.id"
          class="border rounded-lg p-4 flex justify-between bg-gray-100 shadow-sm"
        >
          <div>
            <h4 class="font-semibold text-lg text-gray-600">
              {{ rsvp.event.title }}
            </h4>
            <p class="text-sm text-gray-500">
              {{ formatDate(rsvp.event.date) }} | Seat: {{ rsvp.seat.label }}
            </p>
            <p class="text-sm text-gray-500">
              User: {{ rsvp.user.name }} ({{ rsvp.user.email }})
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from '@/axios'
import Swal from 'sweetalert2'

const rsvps = ref([])
const loading = ref(true)

const fetchRsvps = async () => {
  try {
    const response = await axios.get('/admin/rsvps')
    rsvps.value = response.data
  } catch (error) {
    console.error('Error loading RSVPs:', error)
  } finally {
    loading.value = false
  }
}

const cancelRsvp = async (id) => {
  const confirm = await Swal.fire({
    title: 'Cancel RSVP?',
    text: 'This action will mark the RSVP as canceled.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
  })
  if (!confirm.isConfirmed) return

  // 🔄 Show loading state
  Swal.fire({
    title: 'Canceling RSVP...',
    text: 'Please wait a moment.',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading()
    },
  })

  try {
    await axios.delete(`/rsvps/${id}`)
    await fetchRsvps()

    Swal.fire({
      icon: 'success',
      title: 'Canceled!',
      text: 'RSVP has been canceled.',
      timer: 1500,
      showConfirmButton: false,
    })
  } catch (error) {
    console.error('Error canceling RSVP:', error)
    Swal.fire('Error', 'Failed to cancel RSVP. Please try again.', 'error')
  }
}

const activeRsvps = computed(() =>
  rsvps.value.filter(r => r.status.toLowerCase() !== 'canceled')
)
const canceledRsvps = computed(() =>
  rsvps.value.filter(r => r.status.toLowerCase() === 'canceled')
)

const formatDate = (d) => new Date(d).toLocaleDateString()

onMounted(fetchRsvps)
</script>
