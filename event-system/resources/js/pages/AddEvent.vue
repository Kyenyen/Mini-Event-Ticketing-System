<template>
  <div class="max-w-lg mx-auto mt-8 p-6 bg-white shadow rounded-lg space-y-4">
    <h2 class="text-2xl font-bold text-center mb-4">Add New Event</h2>

    <form @submit.prevent="createEvent" class="space-y-4">
      <div>
        <label class="block font-medium">Title</label>
        <input
          v-model="event.title"
          type="text"
          class="w-full border rounded p-2"
          required
        />
      </div>

      <div>
        <label class="block font-medium">Description</label>
        <textarea
          v-model="event.description"
          class="w-full border rounded p-2"
          rows="3"
        ></textarea>
      </div>

      <div>
        <label class="block font-medium">Date</label>
        <input
          v-model="event.date"
          type="date"
          class="w-full border rounded p-2"
          :min="today"
          required
        />
      </div>

      <div>
        <label class="block font-medium">Location</label>
        <input
          v-model="event.location"
          type="text"
          class="w-full border rounded p-2"
          required
        />
      </div>

      <div>
        <label class="block font-medium">Capacity</label>
        <input
          v-model="event.capacity"
          type="number"
          min="1"
          class="w-full border rounded p-2"
          required
        />
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
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";

const event = ref({
  title: "",
  description: "",
  date: "",
  location: "",
  capacity: "",
});

const loading = ref(false);
const message = ref("");
const today = new Date().toISOString().split("T")[0]; // 🗓️ Prevent past dates

const createEvent = async () => {
  loading.value = true;
  message.value = "";

  try {
    await axios.post("http://127.0.0.1:8000/api/events", event.value);
    message.value = "✅ Event created successfully!";
    event.value = { title: "", description: "", date: "", location: "", capacity: "" };
  } catch (error) {
    message.value = "❌ Failed to create event.";
    console.error("Failed to create event:", error);
  } finally {
    loading.value = false;
  }
};
</script>
