<template>
  <div class="max-w-lg mx-auto mt-8 p-6 bg-white shadow rounded-lg space-y-4">
    <h2 class="text-2xl font-bold text-center mb-4 text-blue-600">Add New Event</h2>

    <form @submit.prevent="createEvent" class="space-y-4">
      <div>
        <label class="block font-medium text-blue-600">Title</label>
        <input
          v-model="event.title"
          type="text"
          class="w-full border rounded p-2"
          required
        />
        <p v-if="errors.title" class="text-red-500 text-sm mt-1">{{ errors.title[0] }}</p>
      </div>

      <div>
        <label class="block font-medium text-blue-600">Description</label>
        <textarea
          v-model="event.description"
          class="w-full border rounded p-2"
          rows="3"
        ></textarea>
        <p v-if="errors.description" class="text-red-500 text-sm mt-1">{{ errors.description[0] }}</p>
      </div>

      <div>
        <label class="block font-medium text-blue-600">Date</label>
        <input
          v-model="event.date"
          type="date"
          class="w-full border rounded p-2"
          :min="today"
          required
        />
        <p v-if="errors.date" class="text-red-500 text-sm mt-1">{{ errors.date[0] }}</p>
      </div>

      <div>
        <label class="block font-medium text-blue-600">Location</label>
        <input
          v-model="event.location"
          type="text"
          class="w-full border rounded p-2"
          required
        />
        <p v-if="errors.location" class="text-red-500 text-sm mt-1">{{ errors.location[0] }}</p>
      </div>

      <div>
        <label class="block font-medium text-blue-600">Capacity</label>
        <input
          v-model="event.capacity"
          type="number"
          min="1"
          class="w-full border rounded p-2"
          required
        />
        <p v-if="errors.capacity" class="text-red-500 text-sm mt-1">{{ errors.capacity[0] }}</p>
      </div>

      <button
        type="submit"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded transition"
        :disabled="loading"
      >
        <span v-if="!loading">Create Event</span>
        <span v-else>Creating...</span>
      </button>

      <p v-if="message" class="text-center text-green-600 font-semibold">
        {{ message }}
      </p>
      <p v-if="errorMessage" class="text-center text-red-600 font-semibold">
        {{ errorMessage }}
      </p>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import axios from '@/axios' // use shared axios config

const event = ref({
  title: "",
  description: "",
  date: "",
  location: "",
  capacity: "",
});

const loading = ref(false);
const message = ref("");
const errorMessage = ref("");
const errors = ref({})
const today = new Date().toISOString().split("T")[0]; // 🗓️ Prevent past dates

const createEvent = async () => {
  loading.value = true;
  message.value = "";

  try {
    // ensure capacity is numeric and use relative API path so shared axios applies auth/cookies
    const payload = { ...event.value, capacity: Number(event.value.capacity) }
    await axios.post('/api/events', payload);
    message.value = "✅ Event created successfully!";
    event.value = { title: "", description: "", date: "", location: "", capacity: "" };
    errors.value = {}
    errorMessage.value = ''
  } catch (error) {
    // field validation errors
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
    } else {
      errorMessage.value = error.response?.data?.message || '❌ Failed to create event.'
    }
    console.error("Failed to create event:", error);
  } finally {
    loading.value = false;
  }
};
</script>
