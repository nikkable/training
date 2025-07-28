<template>
  <div class="page page-auth">
    <div class="page__container">
      <div class="page__header">
        <h2>Форма входа</h2>
      </div>
      <form @submit.prevent="login" class="form">
        <div class="form__field">
          <label for="loginEmail">Email:</label>
          <input type="email" id="loginEmail" v-model="form.email" required>
        </div>
        <div class="form__field">
          <label for="loginPassword">Пароль:</label>
          <input type="password" id="loginPassword" v-model="form.password" required>
        </div>
        <button type="submit" class="form__button">Войти</button>
      </form>
      <p v-if="errorMessage" class="message__error">{{ errorMessage }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '~/stores/auth';
import { useRouter } from 'vue-router';

const form = ref({ email: '', password: '' });
const errorMessage = ref(null);
const router = useRouter();
const authStore = useAuthStore();

const login = async () => {
  errorMessage.value = null;
  try {
    await authStore.login(form.value.email, form.value.password);
    router.push('/profile');
  } catch (err) {
    errorMessage.value = err.message;
    console.error('Login failed:', err);
  }
};
</script>

<style lang="scss">
@import '~/assets/scss/pages.scss';
</style>