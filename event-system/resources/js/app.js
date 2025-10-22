import './bootstrap'
import '../css/app.css' // <-- ensure CSS is imported so Vite bundles Tailwind + your custom styles
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'

const app = createApp(App)
app.use(createPinia())
app.use(router)
app.mount('#app')
