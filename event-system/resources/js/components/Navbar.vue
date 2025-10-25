<template>
  <nav class="bg-white/80 backdrop-blur-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16 items-center">
        <!-- Left: Logo / Brand -->
        <div class="flex items-center space-x-3">
          <router-link to="/" class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-md bg-gradient-to-r from-violet-600 to-teal-400 flex items-center justify-center text-white font-bold">
              ET
            </div>
            <span class="font-semibold text-gray-800">EventTicket</span>
          </router-link>
        </div>

        <!-- Center: optional nav links -->
        <div class="hidden md:flex items-center space-x-6">
          <router-link to="/" class="text-gray-600 hover:text-gray-900">Home</router-link>
          <router-link to="/" class="text-gray-600 hover:text-gray-900">Events</router-link>
          <router-link to="/" class="text-gray-600 hover:text-gray-900">About</router-link>
        </div>

        <!-- Right: Profile / Auth actions -->
        <div class="flex items-center space-x-4">
          <template v-if="user">
            <div class="relative" ref="menuRef">
              <button @click="open = !open" class="flex items-center gap-2 px-3 py-1 rounded-md hover:bg-gray-100">
                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-700">
                  {{ initials }}
                </div>
                <div class="text-left">
                  <div class="text-sm font-medium text-gray-800">{{ user.name }}</div>
                  <div class="text-xs text-gray-500">Role: {{ user.role ?? 'user' }}</div>
                </div>
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>

              <div v-if="open" class="absolute right-0 mt-2 w-44 bg-white border rounded-md shadow-lg py-1 z-20">
                <router-link
                  to="/change-password"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Change Password
                </router-link>
                <button
                  @click="handleLogout"
                  class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100"
                >
                  Logout
                </button>
              </div>
            </div>
          </template>

          <template v-else>
            <router-link to="/login" class="px-3 py-1 rounded-md text-sm bg-blue-600 text-white hover:bg-blue-700">Login</router-link>
            <router-link to="/register" class="px-3 py-1 rounded-md text-sm border border-gray-300 text-blue-600 hover:bg-gray-100">Register</router-link>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()
const open = ref(false)
const menuRef = ref(null)

const user = computed(() => auth.user)

const initials = computed(() => {
  const n = user.value?.name ?? ''
  return n.split(' ').map(s => s[0] ?? '').slice(0,2).join('').toUpperCase() || 'U'
})

onMounted(async () => {
  // Ensure user state is populated (store.getUser uses /api/user)
  if (!auth.user) {
    await auth.getUser().catch(() => {})
  }
  document.addEventListener('click', handleOutsideClick)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleOutsideClick)
})

function handleOutsideClick(e) {
  if (open.value && menuRef.value && !menuRef.value.contains(e.target)) {
    open.value = false
  }
}

async function handleLogout() {
  await auth.logout().catch(() => {})
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  open.value = false
  router.push('/login')
}
</script>

<style scoped>
/* small helper for click.outside directive fallback if not available: use native listener in template via @click.outside plugin or polyfill */
</style>