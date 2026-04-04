<template>
    <div class="layout">
        <nav class="sidebar">
            <h2>SMS</h2>
            <router-link to="/">Dashboard</router-link>
            <router-link to="/classrooms">Classrooms</router-link>
            <router-link v-if="!auth.isStudent" to="/students">Students</router-link>
            <a href="#" @click.prevent="handleLogout">Logout</a>
        </nav>
        <main class="content">
            <h1>Dashboard</h1>
            <div class="cards">
                <div class="card">
                    <h3>Welcome, {{ auth.user?.name }}</h3>
                    <p>Role: <strong>{{ auth.user?.role }}</strong></p>
                </div>
                <div class="card" v-if="auth.isAdmin">
                    <h3>Admin Panel</h3>
                    <p>You can manage all classrooms, students and teachers.</p>
                </div>
                <div class="card" v-if="auth.isTeacher">
                    <h3>Teacher Panel</h3>
                    <p>You can manage your classrooms and students.</p>
                </div>
                <div class="card" v-if="auth.isStudent">
                    <h3>Student Panel</h3>
                    <p>You can view your classroom and update your profile.</p>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

async function handleLogout() {
    auth.logout()
    router.push('/login')
}
</script>

<style scoped>
.layout { display: flex; min-height: 100vh; }
.sidebar {
    width: 220px;
    background: #1e1e2e;
    color: white;
    padding: 2rem 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.sidebar h2 { font-size: 1.5rem; margin-bottom: 1rem; }
.sidebar a {
    color: #a5b4fc;
    text-decoration: none;
    padding: 0.5rem;
    border-radius: 4px;
}
.sidebar a:hover, .sidebar a.router-link-active { background: #4f46e5; color: white; }
.content { flex: 1; padding: 2rem; background: #f0f2f5; }
h1 { margin-bottom: 1.5rem; }
.cards { display: flex; gap: 1rem; flex-wrap: wrap; }
.card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    min-width: 250px;
}
.card h3 { margin-bottom: 0.5rem; }
</style>