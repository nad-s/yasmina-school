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
            <div class="page-header">
                <h1>Classrooms</h1>
                <button v-if="auth.isAdmin" @click="openCreate">+ Add Classroom</button>
            </div>

            <div v-if="loading">Loading...</div>

            <table v-else>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Teacher</th>
                        <th>Students</th>
                        <th v-if="!auth.isStudent">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="classroom in classrooms" :key="classroom.id">
                        <td>{{ classroom.name }}</td>
                        <td>{{ classroom.description || '-' }}</td>
                        <td>{{ classroom.teacher?.name || 'Unassigned' }}</td>
                        <td>{{ classroom.students?.length || 0 }}</td>
                        <td v-if="!auth.isStudent">
                            <button class="btn-edit" @click="openEdit(classroom)">Edit</button>
                            <button class="btn-delete" v-if="auth.isAdmin" @click="deleteClassroom(classroom.id)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Modal -->
            <div v-if="showModal" class="modal-overlay">
                <div class="modal">
                    <h2>{{ editing ? 'Edit Classroom' : 'Add Classroom' }}</h2>
                    <div class="form-group">
                        <label>Name</label>
                        <input v-model="form.name" type="text" />
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <input v-model="form.description" type="text" />
                    </div>
                    <div class="form-group" v-if="auth.isAdmin">
                        <label>Teacher ID</label>
                        <input v-model="form.teacher_id" type="number" placeholder="User ID of teacher" />
                    </div>
                    <div class="modal-actions">
                        <button @click="saveClassroom">Save</button>
                        <button class="btn-cancel" @click="showModal = false">Cancel</button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'
import api from '../api/axios'

const auth = useAuthStore()
const router = useRouter()

const classrooms = ref([])
const loading = ref(true)
const showModal = ref(false)
const editing = ref(null)
const form = ref({ name: '', description: '', teacher_id: '' })

async function fetchClassrooms() {
    loading.value = true
    const res = await api.get('/classrooms')
    classrooms.value = res.data
    loading.value = false
}

function openCreate() {
    editing.value = null
    form.value = { name: '', description: '', teacher_id: '' }
    showModal.value = true
}

function openEdit(classroom) {
    editing.value = classroom.id
    form.value = {
        name: classroom.name,
        description: classroom.description || '',
        teacher_id: classroom.teacher_id || ''
    }
    showModal.value = true
}

async function saveClassroom() {
    const payload = { ...form.value }
    if (!payload.teacher_id) delete payload.teacher_id

    if (editing.value) {
        await api.put(`/classrooms/${editing.value}`, payload)
    } else {
        await api.post('/classrooms', payload)
    }
    showModal.value = false
    fetchClassrooms()
}

async function deleteClassroom(id) {
    if (confirm('Delete this classroom?')) {
        await api.delete(`/classrooms/${id}`)
        fetchClassrooms()
    }
}

function handleLogout() {
    auth.logout()
    router.push('/login')
}

onMounted(fetchClassrooms)
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
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; }
th, td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid #eee; }
th { background: #f8f8f8; font-weight: 600; }
button { padding: 0.4rem 0.8rem; border: none; border-radius: 4px; cursor: pointer; background: #4f46e5; color: white; }
.btn-edit { background: #f59e0b; margin-right: 0.3rem; }
.btn-delete { background: #ef4444; }
.btn-cancel { background: #6b7280; margin-left: 0.5rem; }
.modal-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.5);
    display: flex; justify-content: center; align-items: center;
}
.modal {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    width: 100%;
    max-width: 450px;
}
.form-group { margin-bottom: 1rem; }
label { display: block; margin-bottom: 0.3rem; font-weight: 500; }
input {
    width: 100%;
    padding: 0.6rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
}
.modal-actions { display: flex; justify-content: flex-end; margin-top: 1rem; }
</style>