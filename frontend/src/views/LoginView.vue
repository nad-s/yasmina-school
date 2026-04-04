<template>
    <div class="login-container">
        <div class="login-box">
            <h1>School Management System</h1>
            <h2>Login</h2>
            <form @submit.prevent="handleLogin">
                <div class="form-group">
                    <label>Email</label>
                    <input v-model="email" type="email" placeholder="Enter email" required />
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input v-model="password" type="password" placeholder="Enter password" required />
                </div>
                <p v-if="error" class="error">{{ error }}</p>
                <button type="submit" :disabled="loading">
                    {{ loading ? 'Logging in...' : 'Login' }}
                </button>
            </form>
            <p>Don't have an account? <a href="/register">Register</a></p>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth = useAuthStore()

const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

async function handleLogin() {
    loading.value = true
    error.value = ''
    try {
        await auth.login(email.value, password.value)
        router.push('/')
    } catch (e) {
        error.value = 'Invalid email or password'
    } finally {
        loading.value = false
    }
}
</script>

<style scoped>
.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background: #f0f2f5;
}
.login-box {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 400px;
}
h1 { font-size: 1.2rem; color: #666; margin-bottom: 0.5rem; }
h2 { font-size: 1.8rem; margin-bottom: 1.5rem; }
.form-group { margin-bottom: 1rem; }
label { display: block; margin-bottom: 0.3rem; font-weight: 500; }
input {
    width: 100%;
    padding: 0.6rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
    box-sizing: border-box;
}
button {
    width: 100%;
    padding: 0.75rem;
    background: #4f46e5;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    margin-top: 0.5rem;
}
button:disabled { background: #a5b4fc; }
.error { color: red; font-size: 0.9rem; }
</style>