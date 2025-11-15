import { defineStore } from 'pinia'
import axios from '../axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
  }),

  actions: {
    async getUser() {
      try {
        // Try to fetch current user. Allow cookie-based Sanctum auth (no token required).
        const response = await axios.get('/api/user')
        this.user = response.data
        localStorage.setItem('user', JSON.stringify(this.user))
      } catch (err) {
        this.logout()
      }
    },

    async login(credentials) {
      try {
        await axios.get('/sanctum/csrf-cookie')
        await axios.post('/api/login', credentials)    // <-- single correct call
        await this.getUser()
        return { success: true }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          return { success: false, errors: error.response.data.errors }
        }
        if (error.response && error.response.status === 401) {
          return { success: false, message: 'Invalid email or password.' }
        }
        // prefer server-provided message if available
        const msg = error.response?.data?.message || 'Something went wrong.'
        return { success: false, message: msg }
      }
    },

    async register(data) {
      try {
        await axios.get('/sanctum/csrf-cookie')
        await axios.post('/api/register', data)        // <-- single correct call
        await this.getUser()
        return { success: true }
      } catch (error) {
        // Validation errors
        if (error.response && error.response.status === 422) {
          return { success: false, errors: error.response.data.errors }
        }
        // Conflict (duplicate) or other server message
        const serverMsg = error.response?.data?.message || ''
        // normalize body to string for pattern checks (some servers return HTML/text on 500)
        const bodyStr = typeof error.response?.data === 'string' ? error.response.data : JSON.stringify(error.response?.data || '')

        // If server reports a duplicate/conflict, return it as an email field error so the form highlights it
        if (error.response && (
          error.response.status === 409 ||
          /duplicate|already exists|already exist|unique|1062/i.test(serverMsg) ||
          /duplicate|unique|1062|duplicate entry/i.test(bodyStr)
        )) {
          const messageText = serverMsg || (bodyStr.match(/Duplicate entry.+for key '([^']+)'/)?.[0]) || 'This email is already registered.'
          return { success: false, errors: { email: [messageText] } }
        }

        const msg = serverMsg || 'Something went wrong.'
        return { success: false, message: msg }
      }
    },

    async logout() {
      try {
        // attempt server logout but do not rely on it to clear local state
        await axios.post('/logout')
      } catch (err) {
        // ignore network errors, still clear client state
        console.warn('Logout request failed', err)
      } finally {
        // clear Pinia state and persisted storage
        this.user = null
        this.token = null
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        localStorage.removeItem('role')
      }
    },
  },
})
