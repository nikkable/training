<template>
  <div class="page page-profile">
    <div class="page__container">
      <div class="page__header">
        <h2 class="page__title">Профиль пользователя</h2>
      </div>
      <div v-if="authStore.user" class="user-info">
        <p class="user-info__welcome">Привет, <strong>{{ authStore.user.name }}</strong>!</p>
        <p class="user-info__email">Email: {{ authStore.user.email }}</p>
        <p class="api-status">API Status: {{ profileApiStatus }}</p>
        <div class="buttons">
          <button @click="fetchUserProfile" class="button button--warning">Перезагрузить</button>
          <button @click="logout" class="button button--danger">Выйти</button>
        </div>
        <p v-if="profileError" class="message__error">{{ profileError }}</p>
      </div>
      <div v-else class="message__warning">
        <p>Вы не авторизованы. <NuxtLink to="/login">Войти</NuxtLink>.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watchEffect } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '~/stores/auth';

const authStore = useAuthStore();
const router = useRouter();

const profileApiStatus = ref('Idle');
const profileError = ref(null);

watchEffect(() => {
  if (!authStore.isAuthenticated && process.client) {
    router.push('/login');
  }
});

const fetchUserProfile = async () => {
  profileApiStatus.value = 'Loading...';
  profileError.value = null;
  try {
    const data = await authStore.fetchUserProfile();
    profileApiStatus.value = data ? 'Authorized' : 'Unauthorized';
  } catch (err) {
    profileError.value = err.message;
    profileApiStatus.value = 'Error fetching profile';
  }
};

const logout = async () => {
  await authStore.logout();
};
</script>

<style lang="scss">
@import '~/assets/scss/pages.scss';

.page {
  max-width: 600px;
  margin: 30px auto;
  padding: 20px;
  border: 1px solid #d4edda;
  background-color: #f8fcf8;
  border-radius: 6px;
}

.page__container {
  padding: 20px;
}

.page__header {
  margin-bottom: 20px;
}

.page__title {
  color: #28a745;
  text-align: center;
}

.user-info {
  margin-bottom: 20px;
}

.user-info__welcome {
  font-size: 1.1em;
  margin-bottom: 10px;
}

.user-info__email {
  margin-bottom: 5px;
}

.api-status {
  margin-bottom: 10px;
}

.buttons {
  margin-bottom: 10px;
}

.button {
  padding: 8px 12px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
  --primary-color: #4CAF50;
  --primary-color-dark: #409148;
  --secondary-color: #008CBA;
  --error-color: #dc3545;
  --error-color-dark: #c82e3c;
  --warning-color: #ffc107;
  --warning-color-dark: #e0a800;
  --text-color: #444;
  --text-light-color: #555;
  --border-color: #ddd;

  &--warning {
    background-color: var(--warning-color);
    color: var(--text-color);

    &:hover {
      background-color: var(--warning-color-dark);
    }
  }

  &--danger {
    background-color: var(--error-color);
    color: white;

    &:hover {
      background-color: var(--error-color-dark);
    }
  }
}

.message__error {
  color: red;
  margin-top: 10px;
}

.message__warning {
  text-align: center;
  color: #666;
}
</style>