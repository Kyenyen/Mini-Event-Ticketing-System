import { createRouter, createWebHistory } from 'vue-router'
import Login from '../pages/Login.vue'
import Register from '../pages/Register.vue'
import CalendarEvents from '../pages/Calendar.vue'
import Home from '../pages/Home.vue'
import ChangePassword from '../pages/ChangePassword.vue'
import EventsList from '../pages/EventsList.vue'
import EventDetail from '../pages/EventDetail.vue'
import AddEvent from '../pages/AddEvent.vue'
import EditEvent from '../pages/EditEvent.vue'
import RsvpList from '../pages/RSVPList.vue'
import AdminRsvpList from '../pages/AdminRSVPList.vue'

// Define routes
const routes = [
  { path: '/', component: Home },
  { path: '/login', component: Login },
  { path: '/register', component: Register },
  { path: '/calendar-events', component: CalendarEvents },
  { path: '/change-password', component: ChangePassword, meta: { requiresAuth: true } },
  { path: '/events', name: 'Events', component: EventsList },
  { path: '/events/add-event', name: 'AddEvent', component: AddEvent, meta: { requiresAuth: true, role: 'admin' } },
  { path: '/events/:id', name: 'EventDetail', component: EventDetail },
  { path: '/events/:id/edit', component: EditEvent, meta: { requiresAuth: true, role: 'admin' } },
  { path: '/my-rsvps', name: 'MyRsvps', component: RsvpList, meta: { requiresAuth: true } },
  { path: '/admin/rsvps', name: 'AdminManageRsvp', component: AdminRsvpList, meta: { requiresAuth: true, role: 'admin' } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// ✅ Add a navigation guard to check auth and role
router.beforeEach((to, from, next) => {
  const user = JSON.parse(localStorage.getItem('user'))

  if (to.meta.requiresAuth && !user) {
    next('/login')
  } else if (to.meta.role && user.role !== to.meta.role) {
    next('/')
  } else {
    next()
  }
})

export default router
