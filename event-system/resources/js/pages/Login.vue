<template>
  <div class="max-w-md mx-auto p-6 bg-white rounded-lg shadow-md mt-10">
    <h2 class="text-2xl font-semibold mb-4 text-center">Login</h2>

    <form @submit.prevent="login">
      <div class="mb-4">
        <label class="block mb-1 font-medium">Email</label>
        <input
          v-model="form.email"
          type="email"
          class="w-full border rounded p-2"
          required
        />
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">Password</label>
        <input
          v-model="form.password"
          type="password"
          class="w-full border rounded p-2"
          required
        />
      </div>

      <div v-if="error" class="text-red-500 text-sm mb-3">{{ error }}</div>
      <div v-if="success" class="text-green-500 text-sm mb-3">{{ success }}</div>

      <button
        type="submit"
        class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded"
      >
        Login
      </button>
    </form>
  </div>
</template>

<script setup>
import axios from "axios";
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

const form = ref({
  email: "",
  password: "",
});

const error = ref("");
const success = ref("");

async function login() {
  error.value = "";
  success.value = "";

  try {
    const response = await axios.post("http://127.0.0.1:8000/api/login", form.value);

    // Save the token
    localStorage.setItem("token", response.data.token);
    localStorage.setItem("user", JSON.stringify(response.data.user));

    success.value = "Login successful! Redirecting...";
    setTimeout(() => router.push("/"), 1000);
  } catch (err) {
    console.error(err);
    error.value = err.response?.data?.message || "Invalid credentials or server error.";
  }
}
</script>
