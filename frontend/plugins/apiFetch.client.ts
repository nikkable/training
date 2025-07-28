import { defineNuxtPlugin, useRuntimeConfig } from '#app'
import { useAuthStore } from '~/stores/auth'

export default defineNuxtPlugin((nuxtApp) => {
  // console.log('API Fetch plugin initialized');

  if (typeof window === 'undefined') {
    return;
  }

  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase || ''
  const authStore = useAuthStore()

  const apiFetch = async (endpoint: string, options: { headers?: Record<string, string>; method?: string, body?: any; } = {}) => {
    const headers: Record<string, string> = {
      'Accept': 'application/json',
      // 'Content-Type': 'application/json',
      ...options.headers,
    }

    if (authStore.accessToken) {
      headers['Authorization'] = `Bearer ${authStore.accessToken}`
    }

    let requestBody = options.body;
    if (options.method === 'POST' || options.method === 'PUT' || options.method === 'PATCH') {
      if (options.body) {
        requestBody = JSON.stringify(options.body);
        headers['Content-Type'] = 'application/json';
      } else {
      }
    }

    const response = await fetch(`${apiBase}${endpoint}`, {
      ...options,
      headers,
      body: requestBody,
    })

    if (!response.ok) {
      const errorData = await response.json().catch(() => ({ message: response.statusText }))
      const errorMessage = errorData.message || `HTTP error! status: ${response.status}`
      console.error(`API Error on ${endpoint}:`, errorData)
      throw new Error(errorMessage)
    }

    return response.json()
  }

  const getCsrfCookie = async () => {
    const response = await fetch(`${apiBase}/sanctum/csrf-cookie`, {
      credentials: 'include',
    })

    if (!response.ok) {
      const errorData = await response.json().catch(() => ({ message: response.statusText }))
      const errorMessage = errorData.message || `HTTP error! status: ${response.status}`
      console.error('CSRF Cookie Error:', errorData)
      throw new Error(errorMessage)
    }
  }

  nuxtApp.vueApp.config.globalProperties.$apiFetch = apiFetch;
  nuxtApp.vueApp.config.globalProperties.$getCsrfCookie = getCsrfCookie;
  nuxtApp.vueApp.config.globalProperties.$authStore = authStore;

  return {
    provide: {
      apiFetch,
      getCsrfCookie,
      authStore
    }
  }
})
