import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [
    {
        path: '/login',
        name: 'Login',
        component: () => import('../views/LoginView.vue'),
        meta: { guestOnly: true }
    },
    {
        path: '/register',
        name: 'Register',
        component: () => import('../views/RegisterView.vue'),
        meta: { guestOnly: true }
    },
    {
        path: '/',
        name: 'Dashboard',
        component: () => import('../views/DashboardView.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/classrooms',
        name: 'Classrooms',
        component: () => import('../views/ClassroomsView.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/students',
        name: 'Students',
        component: () => import('../views/StudentsView.vue'),
        meta: { requiresAuth: true }
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach((to, from, next) => {
    const auth = useAuthStore()

    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        next('/login')
    } else if (to.meta.guestOnly && auth.isAuthenticated) {
        next('/')
    } else {
        next()
    }
})

export default router