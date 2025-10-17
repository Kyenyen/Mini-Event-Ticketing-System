<template>
  <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-4 text-center">Login</h2>

    <form @submit.prevent="submitLogin">
      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Email</label>
        <input v-model="email" type="email" class="w-full p-2 border rounded" />
        <p v-if="errors.email" class="text-red-500 text-sm">{{ errors.email[0] }}</p>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Password</label>
        <input v-model="password" type="password" class="w-full p-2 border rounded" />
        <p v-if="errors.password" class="text-red-500 text-sm">{{ errors.password[0] }}</p>
      </div>

      <p v-if="message" class="text-red-500 text-sm mb-3">{{ message }}</p>

      <button type="submit" class="bg-blue-600 text-white w-full py-2 rounded hover:bg-blue-700">
        Login
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'

const email = ref('')
const password = ref('')
const errors = ref({})
const message = ref('')
const auth = useAuthStore()

const submitLogin = async () => {
  errors.value = {}
  message.value = ''
  const res = await auth.login({ email: email.value, password: password.value })
  if (!res.success) {
    if (res.errors) errors.value = res.errors
    if (res.message) message.value = res.message
  } else {
    message.value = ''
    // redirect to dashboard or home
  }
}
</script>
