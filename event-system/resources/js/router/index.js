import { createRouter, createWebHistory } from 'vue-router'
import Login from '../pages/Login.vue'
import Register from '../pages/Register.vue'
import Home from '../pages/Home.vue'
import ChangePassword from '../pages/ChangePassword.vue'

const routes = [
  { path: '/', component: Home },
  { path: '/login', component: Login },
  { path: '/register', component: Register },
  { path: '/change-password', component: ChangePassword },
]

export default createRouter({
  history: createWebHistory(),
  routes,
})
