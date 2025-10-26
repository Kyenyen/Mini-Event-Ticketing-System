<template>
  <div class="max-w-lg mx-auto mt-10 bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold mb-6 text-center">Edit Event</h2>

    <form @submit.prevent="updateEvent" class="space-y-4">
      <div>
        <label class="block mb-1 font-medium">Title</label>
        <input
          v-model="form.title"
          type="text"
          required
          class="w-full border px-3 py-2 rounded-lg focus:ring focus:ring-blue-300"
        />
      </div>

      <div>
        <label class="block mb-1 font-medium">Description</label>
        <textarea
          v-model="form.description"
          required
          class="w-full border px-3 py-2 rounded-lg focus:ring focus:ring-blue-300"
        ></textarea>
      </div>

      <div>
        <label class="block mb-1 font-medium">Date</label>
        <input
          v-model="form.date"
          type="date"
          :min="today"
          required
          class="w-full border px-3 py-2 rounded-lg focus:ring focus:ring-blue-300"
        />
      </div>

      <div>
        <label class="block mb-1 font-medium">Location</label>
        <input
          v-model="form.location"
          type="text"
          required
          class="w-full border px-3 py-2 rounded-lg focus:ring focus:ring-blue-300"
        />
      </div>

      <button
        type="submit"
        :disabled="loading"
        class="w-full bg-yellow-500 text-white py-2 rounded-lg hover:bg-yellow-600 transition disabled:opacity-50"
      >
        {{ loading ? "Updating Event..." : "Save Changes" }}
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();
const loading = ref(false);
const today = new Date().toISOString().split("T")[0];

const form = ref({
  title: "",
  description: "",
  date: "",
  location: "",
});

onMounted(async () => {
  const { data } = await axios.get(`http://127.0.0.1:8000/api/events/${route.params.id}`);
  form.value = {
    title: data.title,
    description: data.description,
    date: data.date,
    location: data.location,
  };
});

async function updateEvent() {
  loading.value = true;
  try {
    await axios.put(
      `http://127.0.0.1:8000/api/events/${route.params.id}`,
      form.value,
      { headers: { Authorization: `Bearer ${localStorage.getItem("token")}` } }
    );

    alert("Event updated successfully!");
    router.push(`/events/${route.params.id}`);
  } catch (error) {
    console.error(error);
    alert(error.response?.data?.message || "Failed to update event.");
  } finally {
    loading.value = false;
  }
}
</script>
