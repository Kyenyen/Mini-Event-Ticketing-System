import { defineStore } from 'pinia'
import axios from '../axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null, // load from localStorage
    token: localStorage.getItem('token') || null,
  }),

  actions: {
    async getUser() {
      try {
        const token = localStorage.getItem('token')
        if (!token) return

        const response = await axios.get('http://127.0.0.1:8000/api/user', {
          headers: { Authorization: `Bearer ${token}` },
        })
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
        return { success: false, message: 'Something went wrong.' }
      }
    },

    async register(data) {
      try {
        await axios.get('/sanctum/csrf-cookie')
        await axios.post('/api/register', data)        // <-- single correct call
        await this.getUser()
        return { success: true }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          return { success: false, errors: error.response.data.errors }
        }
        return { success: false, message: 'Something went wrong.' }
      }
    },

    async logout() {
      try {
        await axios.post('/api/logout')
        this.user = null
      } catch {
        console.log('Logout failed')
      }
    },
  },
})
