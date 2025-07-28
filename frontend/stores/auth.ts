import { defineStore } from 'pinia';
import { useRuntimeConfig } from '#app';

interface User {
  id: number;
  name: string;
  email: string;
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as User | null,
    accessToken: null as string | null,
  }),
  getters: {
    isAuthenticated: (state) => state.user !== null,
  },
  actions: {
    async login(email: string, password: string) {
      try {
        const config = useRuntimeConfig();

        if (typeof window === 'undefined') {
          throw new Error('Login must be performed on client side');
        }

        const response = await fetch(`${config.public.apiBase}/api/login`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ email, password }),
          credentials: 'include',
        });

        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.message || 'Login failed');
        }

        const data = await response.json();
        this.accessToken = data.access_token;
        localStorage.setItem('access_token', data.access_token);

        await this.fetchUserProfile();
      } catch (error) {
        console.error('Login error:', error);
        throw error;
      }
    },

    async register(form) {
      try {
        const config = useRuntimeConfig();

        if (typeof window === 'undefined') {
          throw new Error('Register must be performed on client side');
        }

        const response = await fetch(`${config.public.apiBase}/api/register`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(form),
          credentials: 'include',
        });

        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.message || 'Register failed');
        }

        const data = await response.json();
        this.accessToken = data.access_token;
        localStorage.setItem('access_token', data.access_token);

        await this.fetchUserProfile();
      } catch (error) {
        console.error('Login error:', error);
        throw error;
      }
    },

    async fetchUserProfile() {
      try {
        const config = useRuntimeConfig();

        if (typeof window === 'undefined') {
          return null;
        }

        const token = this.accessToken || localStorage.getItem('access_token');
        if (!token) {
          this.user = null;
          this.accessToken = null;
          localStorage.removeItem('access_token');
          throw new Error('No access token found for profile fetch');
        }

        const response = await fetch(`${config.public.apiBase}/api/user`, {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
          },
          credentials: 'include',
        });

        if (!response.ok) {
          if (response.status === 401 || response.status === 403) {
            this.user = null;
            this.accessToken = null;
            localStorage.removeItem('access_token');
          }
          const errorData = await response.json();
          throw new Error(errorData.message || 'Failed to fetch user profile');
        }

        const data = await response.json();
        this.user = data;
        return data;
      } catch (error) {
        console.error('Profile fetch error:', error);
        throw error;
      }
    },

    async logout() {
      try {
        const config = useRuntimeConfig();

        if (typeof window === 'undefined') {
          throw new Error('Logout must be performed on client side');
        }

        if (this.accessToken) {
          await fetch(`${config.public.apiBase}/api/logout`, {
            method: 'POST',
            headers: {
              'Authorization': `Bearer ${this.accessToken}`,
            },
            credentials: 'include',
          });
        }
      } catch (error) {
        console.error('Logout error:', error);
      } finally {
        this.user = null;
        this.accessToken = null;
        localStorage.removeItem('access_token');
      }
    },

    // Инициализация состояния при загрузке
    async initialize() {
      if (typeof window !== 'undefined') {
        const savedToken = localStorage.getItem('access_token');
        if (savedToken && !this.accessToken) {
          this.accessToken = savedToken;
          try {
            await this.fetchUserProfile();
          } catch (err) {
            console.error('Failed to restore user session:', err);

            // Очищаем все
            this.accessToken = null;
            this.user = null;
            localStorage.removeItem('access_token');
          }
        }
      }
    }
  },
});
