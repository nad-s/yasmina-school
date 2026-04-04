import { defineStore } from 'pinia'
import api from '../api/axios'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: JSON.parse(localStorage.getItem('user')) || null,
        token: localStorage.getItem('token') || null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        isAdmin: (state) => state.user?.role === 'admin',
        isTeacher: (state) => state.user?.role === 'teacher',
        isStudent: (state) => state.user?.role === 'student',
    },

    actions: {
        async login(email, password) {
            const response = await api.post('/login', { email, password })
            this.token = response.data.token
            this.user = response.data.user
            localStorage.setItem('token', this.token)
            localStorage.setItem('user', JSON.stringify(this.user))
        },

        async register(name, email, password) {
            const response = await api.post('/register', { name, email, password })
            this.token = response.data.token
            this.user = response.data.user
            localStorage.setItem('token', this.token)
            localStorage.setItem('user', JSON.stringify(this.user))
        },

        logout() {
            api.post('/logout')
            this.token = null
            this.user = null
            localStorage.removeItem('token')
            localStorage.removeItem('user')
        }
    }
})