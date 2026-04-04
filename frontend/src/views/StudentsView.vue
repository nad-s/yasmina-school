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
                <h1>Students</h1>
                <button v-if="auth.isAdmin" @click="openCreate">+ Add Student</button>
            </div>

            <div v-if="loading">Loading...</div>

            <table v-else>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Date of Birth</th>
                        <th>Grade</th>
                        <th>Classroom</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="student in students" :key="student.id">
                        <td>{{ student.user?.name }}</td>
                        <td>{{ student.user?.email }}</td>
                        <td>{{ student.date_of_birth || '-' }}</td>
                        <td>{{ student.grade || '-' }}</td>
                        <td>{{ student.classroom?.name || 'Unassigned' }}</td>
                        <td>
                            <button class="btn-edit" @click="openEdit(student)">Edit</button>
                            <button class="btn-delete" v-if="auth.isAdmin" @click="deleteStudent(student.id)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="pagination">
                <button :disabled="currentPage === 1" @click="changePage(currentPage - 1)">Previous</button>
                <span>Page {{ currentPage }} of {{ totalPages }}</span>
                <button :disabled="currentPage === totalPages" @click="changePage(currentPage + 1)">Next</button>
            </div>
            <!-- Modal -->
            <div v-if="showModal" class="modal-overlay">
                <div class="modal">
                    <h2>{{ editing ? 'Edit Student' : 'Add Student' }}</h2>
                    <div class="form-group" v-if="!editing">
                        <label>User ID</label>
                        <input v-model="form.user_id" type="number" placeholder="User ID" />
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input v-model="form.date_of_birth" type="date" />
                    </div>
                    <div class="form-group" v-if="auth.isAdmin || auth.isTeacher">
                        <label>Classroom ID</label>
                        <input v-model="form.assigned_class_id" type="number" placeholder="Classroom ID" />
                    </div>
                    <div class="form-group" v-if="auth.isAdmin || auth.isTeacher">
                        <label>Grade</label>
                        <input v-model="form.grade" type="text" placeholder="e.g. A, B+, 90%" />
                    </div>
                    <div class="modal-actions">
                        <button @click="saveStudent">Save</button>
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

const students = ref([])
const loading = ref(true)
const showModal = ref(false)
const editing = ref(null)
const form = ref({ user_id: '', date_of_birth: '', assigned_class_id: '', grade: '' })

const currentPage = ref(1)
const totalPages = ref(1)

async function fetchStudents() {
    loading.value = true
    const res = await api.get(`/students?page=${currentPage.value}`)
    students.value = res.data.data
    totalPages.value = res.data.last_page
    loading.value = false
}

async function changePage(page) {
    currentPage.value = page
    fetchStudents()
}

function openCreate() {
    editing.value = null
    form.value = { user_id: '', date_of_birth: '', assigned_class_id: '', grade: '' }
    showModal.value = true
}

function openEdit(student) {
    editing.value = student.id
    form.value = {
        date_of_birth: student.date_of_birth || '',
        assigned_class_id: student.assigned_class_id || '',
        grade: student.grade || ''
    }
    showModal.value = true
}

async function saveStudent() {
    const payload = { ...form.value }
    Object.keys(payload).forEach(k => { if (payload[k] === '') delete payload[k] })

    if (editing.value) {
        await api.put(`/students/${editing.value}`, payload)
    } else {
        await api.post('/students', payload)
    }
    showModal.value = false
    fetchStudents()
}

async function deleteStudent(id) {
    if (confirm('Delete this student?')) {
        await api.delete(`/students/${id}`)
        fetchStudents()
    }
}

function handleLogout() {
    auth.logout()
    router.push('/login')
}

onMounted(fetchStudents)
</script>

<style scoped>
.pagination {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-top: 1rem;
}
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