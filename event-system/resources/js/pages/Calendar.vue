<template>
  <div class="p-6 text-blue-600">
    <!-- Month Navigation -->
    <div class="flex justify-between items-center mb-4">
      <button
        type="button"
        class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300"
        @click="prevMonth"
      >
        &larr; Previous
      </button>

      <h2 class="text-2xl font-semibold text-blue-600">
        {{ currentDate.toLocaleString('default', { month: 'long' }) }} {{ currentDate.getFullYear() }}
      </h2>

      <button
        type="button"
        class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300"
        @click="nextMonth"
      >
        Next &rarr;
      </button>
    </div>

    <!-- Calendar Header -->
    <div class="grid grid-cols-7 gap-2 font-bold text-center mb-2">
      <div>Mon</div>
      <div>Tue</div>
      <div>Wed</div>
      <div>Thu</div>
      <div>Fri</div>
      <div>Sat</div>
      <div>Sun</div>
    </div>

    <!-- Calendar Days -->
    <div class="grid grid-cols-7 gap-2">
      <div
        v-for="d in calendarDays"
        :key="d.full || d.date"
        class="border p-2 rounded shadow-sm min-h-[80px] bg-white"
        :class="{
          'invisible': d.date == null,
          'border-blue-500 bg-blue-200': isToday(d)
        }"
      >
        <div class="font-semibold">{{ d.date ? d.date : '' }}</div>

        <div
          v-for="ev in d.events"
          :key="ev.id"
          class="text-sm rounded px-1 mt-1 cursor-pointer"
          :class="{
          'bg-blue-200 border-blue-500 hover:bg-blue-300': (user.value?.role === 'admin' || !ev.rsvp_user_ids?.includes(user.value.id)) && !isPast(ev),
          'bg-gray-200 text-gray-400 cursor-not-allowed': isPast(ev)
          }"
          @click="!isPast(ev) && openEvent(ev)"
        >
          {{ ev.title }}
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-lg shadow-lg p-6 w-96 relative z-50">
        <button
          type="button"
          class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl"
          @click="closeModal"
        >
          &times;
        </button>
        <h3 class="text-xl font-semibold mb-2">{{ selectedEvent.title }}</h3>
        <p class="text-gray-600 mb-2">Date: {{ selectedEvent.date }}</p>
        <p class="text-gray-700">{{ selectedEvent.description || 'No description' }}</p>
        <p class="text-gray-700">Location: {{ selectedEvent.location || 'TBA' }}</p>
        <p class="text-gray-700">Capacity: {{ selectedEvent.capacity || 'TBA' }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from '../axios';
import { useAuthStore } from '../stores/auth';

const currentDate = ref(new Date());
const events = ref([]);
const auth = useAuthStore();
const user = computed(() => auth.user);

// Modal
const showModal = ref(false);
const selectedEvent = ref({});

// Load events
const loadEvents = async () => {
  try {
    const res = await axios.get('/api/calendar-events');
    events.value = Array.isArray(res.data) ? res.data : res.data.events || res.data.data || [];
  } catch (err) {
    console.error('Failed to load events:', err);
  }
};

onMounted(() => loadEvents());

// Helpers for current month/year
const year = computed(() => currentDate.value.getFullYear());
const month = computed(() => currentDate.value.getMonth());
const todayStr = computed(() => {
  const d = new Date();
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
});

const firstDay = computed(() => new Date(year.value, month.value, 1));
const lastDay = computed(() => new Date(year.value, month.value + 1, 0));

// Build calendar
const calendarDays = computed(() => {
  const days = [];
  const startDay = firstDay.value.getDay();
  const daysInMonth = lastDay.value.getDate();
  const offset = (startDay === 0 ? 6 : startDay - 1);

  for (let i = 0; i < offset; i++) days.push({ date: null, events: [] });

  for (let day = 1; day <= daysInMonth; day++) {
    const dateStr = `${year.value}-${String(month.value + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    const dayEvents = events.value.filter(ev => ev.date?.startsWith(dateStr));
    days.push({ date: day, full: dateStr, events: dayEvents });
  }
  return days;
});

// Today highlight
const isToday = (day) => day.full === todayStr.value;

// Checks if an event is in the past
const isPast = (ev) => {
  if (!ev.date) return false;
  const today = new Date();
  const evDate = new Date(ev.date);
  return evDate < today.setHours(0,0,0,0);
};

// Modal functions
const openEvent = (ev) => {
  selectedEvent.value = ev;
  showModal.value = true;
};
const closeModal = () => {
  showModal.value = false;
  selectedEvent.value = {};
};

// Month navigation
const prevMonth = () => {
  currentDate.value = new Date(year.value, month.value - 1, 1);
};
const nextMonth = () => {
  currentDate.value = new Date(year.value, month.value + 1, 1);
};
</script>
