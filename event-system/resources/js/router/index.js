import { createRouter, createWebHistory } from 'vue-router'
import Login from '../pages/Login.vue'
import Register from '../pages/Register.vue'
import Home from '../pages/Home.vue'
import ChangePassword from '../pages/ChangePassword.vue'
import EventsList from '../pages/EventsList.vue'
import EventDetail from "../pages/EventDetail.vue";
import AddEvent from '../pages/AddEvent.vue'
import EditEvent from "@/pages/EditEvent.vue";

const routes = [
  { path: '/', component: Home },
  { path: '/login', component: Login },
  { path: '/register', component: Register },
  { path: '/change-password', component: ChangePassword },
  { path: '/events', name: 'Events', component: EventsList },
  { path: '/events/add-event', name: 'AddEvent', component: AddEvent },
  { path: '/events/:id', name: 'EventDetail', component: EventDetail },
  { path: "/events/:id/edit", component: EditEvent },
]

export default createRouter({
  history: createWebHistory(),
  routes,
})