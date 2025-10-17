import { defineStore } from 'pinia'
import axios from '../axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
  }),

  actions: {
    async getUser() {
      try {
        const res = await axios.get('/api/user')
        this.user = res.data
      } catch {
        this.user = null
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
