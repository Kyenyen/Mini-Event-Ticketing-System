<template>
  <div class="max-w-md mx-auto mt-12 p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold text-center mb-4 text-blue-600">Change Password</h2>

    <form @submit.prevent="changePassword">
      <div class="mb-4">
        <label class="block font-medium mb-1 text-blue-600">Current Password</label>
        <input v-model="form.current_password" type="password" class="w-full border rounded p-2" required />
      </div>

      <div class="mb-4">
        <label class="block font-medium mb-1 text-blue-600">New Password</label>
        <input v-model="form.new_password" type="password" class="w-full border rounded p-2" required />
      </div>

      <div class="mb-4">
        <label class="block font-medium mb-1 text-blue-600">Confirm New Password</label>
        <input v-model="form.new_password_confirmation" type="password" class="w-full border rounded p-2" required />
      </div>

      <div v-if="error" class="text-red-500 text-sm mb-3">{{ error }}</div>
      <div v-if="success" class="text-green-500 text-sm mb-3">{{ success }}</div>

      <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">
        Update Password
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";

const form = ref({
  current_password: "",
  new_password: "",
  new_password_confirmation: "",
});

const error = ref("");
const success = ref("");

async function changePassword() {
  error.value = "";
  success.value = "";

  try {
    const token = localStorage.getItem("token");
    await axios.post(
      "http://127.0.0.1:8000/api/change-password",
      form.value,
      { headers: { Authorization: `Bearer ${token}` } }
    );

    success.value = "Password updated successfully!";
    form.value = { current_password: "", new_password: "", new_password_confirmation: "" };
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to change password.";
  }
}
</script>
