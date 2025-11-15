import axios from 'axios';

// Point to the Laravel app root. API endpoints are under /api so calls use '/api/...'
axios.defaults.baseURL = 'http://127.0.0.1:8000';
axios.defaults.withCredentials = true; // allow Sanctum cookie flow

axios.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default axios;
