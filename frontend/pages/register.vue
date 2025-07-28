<template>
  <div class="page page-auth">
    <div class="page__container">
      <div class="page__header">
        <h2>Регистрация</h2>
      </div>
      <form @submit.prevent="register" class="form">
        <div class="form__field">
          <label for="regName">Имя:</label>
          <input type="text" id="regName" v-model="form.name" required>
        </div>
        <div class="form__field">
          <label for="regEmail">Email:</label>
          <input type="email" id="regEmail" v-model="form.email" required>
        </div>
        <div class="form__field">
          <label for="regPassword">Пароль:</label>
          <input type="password" id="regPassword" v-model="form.password" required>
        </div>
        <div class="form__field">
          <label for="regPasswordConfirm">Повторить пароль:</label>
          <input type="password" id="regPasswordConfirm" v-model="form.password_confirmation" required>
        </div>
        <button type="submit" class="form__button">Зарегистрироваться</button>
      </form>
      <p v-if="errorMessage" class="message__error">{{ errorMessage }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '~/stores/auth';

const form = ref({ name: '', email: '', password: '', password_confirmation: '' });
const errorMessage = ref(null);
const router = useRouter();
const authStore = useAuthStore();

const register = async () => {
  errorMessage.value = null;
  try {
    await authStore.register(form.value);
    router.push('/profile');
  } catch (err) {
    errorMessage.value = err.message;
    console.error('Registration failed:', err);
  }
};
</script>

<style lang="scss">
@import '~/assets/scss/pages.scss';
</style>