<template>
  <div>
    <Navbar />
    <main class="pt-6">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import Navbar from './components/Navbar.vue'
</script>

<style scoped>
</style>
