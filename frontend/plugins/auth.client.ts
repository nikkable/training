import { defineNuxtPlugin } from '#app';
import { useAuthStore } from '~/stores/auth';

export default defineNuxtPlugin(async (nuxtApp) => {
    const authStore = useAuthStore(nuxtApp.$pinia);

    if (process.client) {
        await authStore.initialize();
    }
});