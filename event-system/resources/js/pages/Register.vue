<template>
  <div class="max-w-md mx-auto mt-20 bg-white shadow-lg rounded-xl p-6">
    <h2 class="text-2xl font-bold text-center mb-6 text-blue-600">Create an Account</h2>

    <form @submit.prevent="submitRegister">
      <div class="mb-4">
        <label class="block text-blue-600 mb-1">Name</label>
        <input
          v-model="form.name"
          type="text"
          class="w-full border rounded px-3 py-2"
          placeholder="Your name"
        />
        <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name[0] }}</p>
      </div>

      <div class="mb-4">
        <label class="block text-blue-600 mb-1">Email</label>
        <input
          v-model="form.email"
          type="email"
          class="w-full border rounded px-3 py-2"
          placeholder="you@example.com"
        />
        <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email[0] }}</p>
      </div>

      <div class="mb-4">
        <label class="block text-blue-600 mb-1">Password</label>
        <input
          v-model="form.password"
          type="password"
          class="w-full border rounded px-3 py-2"
          placeholder="********"
        />
        <p v-if="errors.password" class="text-red-500 text-sm mt-1">{{ errors.password[0] }}</p>
      </div>

      <div class="mb-6">
        <label class="block text-blue-600 mb-1">Confirm Password</label>
        <input
          v-model="form.password_confirmation"
          type="password"
          class="w-full border rounded px-3 py-2"
          placeholder="********"
        />
        <p v-if="errors.password_confirmation" class="text-red-500 text-sm mt-1">{{ errors.password_confirmation[0] }}</p>
      </div>

      <button
        type="submit"
        class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded transition"
        :disabled="loading"
      >
        <span v-if="!loading">Register</span>
        <span v-else>Registering...</span>
      </button>

      <p v-if="message" class="text-red-500 text-center mt-3">{{ message }}</p>
      <p v-if="success" class="text-green-600 text-center mt-3">{{ success }}</p>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const errors = ref({})
const message = ref('')
const success = ref('')
const loading = ref(false)

const submitRegister = async () => {
  // reset
  errors.value = {}
  message.value = ''
  success.value = ''

  // Client-side validation
  const clientErrors = {}
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!form.value.name || form.value.name.trim().length === 0) {
    clientErrors.name = ['Name is required']
  }

  if (!form.value.email || form.value.email.trim().length === 0) {
    clientErrors.email = ['Email is required']
  } else if (!emailRegex.test(form.value.email)) {
    clientErrors.email = ['Invalid email format']
  }

  if (!form.value.password) {
    clientErrors.password = ['Password is required']
  } else if (form.value.password.length < 8) {
    clientErrors.password = ['Password must be at least 8 characters']
  }

  if (!form.value.password_confirmation) {
    clientErrors.password_confirmation = ['Confirm password is required']
  } else if (form.value.password !== form.value.password_confirmation) {
    clientErrors.password_confirmation = ["Confirm password didn't match"]
  }

  if (Object.keys(clientErrors).length > 0) {
    errors.value = clientErrors
    return
  }

  loading.value = true
  const res = await auth.register(form.value)
  loading.value = false

  // server-side validation / business errors
  if (!res.success) {
    // server returns validation errors in res.errors (field => [messages])
    if (res.errors) {
      errors.value = res.errors
    }

    // handle common message cases: email already exists, weak password, etc.
    if (res.message) {
      // if message mentions email exists, show near email field
      if (/(already exist|already exists|taken)/i.test(res.message)) {
        errors.value.email = [res.message]
      } else if (/password/i.test(res.message)) {
        errors.value.password = [res.message]
      } else {
        message.value = res.message
      }
    }
  } else {
    success.value = 'Registration successful! Redirecting to login...'
    setTimeout(() => router.push('/login'), 1500)
  }
}
</script>
