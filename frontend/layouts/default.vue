<template>
  <div>
    <div class="page__container">
      <div class="page__header">
        <h2>Тренировочное приложение</h2>
      </div>
      <div class="page__nav">
        <NuxtLink to="/" class="nav-link">Домой</NuxtLink>
        <template v-if="!authStore.isAuthenticated">
          <NuxtLink to="/register" class="nav-link">Регистрация</NuxtLink>
          <NuxtLink to="/login" class="nav-link">Авиторизация</NuxtLink>
        </template>
        <NuxtLink to="/profile" class="nav-link">Профиль</NuxtLink>

        <template v-if="authStore.isAuthenticated">
          <NuxtLink to="/parse" class="nav-link">Парсинг</NuxtLink>
        </template>
      </div>
    </div>

    <slot />
  </div>
</template>

<script setup>
import { useAuthStore } from "~/stores/auth";

const authStore = useAuthStore();
</script>

<style>
body {
  margin: 0;
  font-family: sans-serif;
}
</style>