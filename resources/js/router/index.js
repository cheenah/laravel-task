import { createRouter, createWebHistory } from 'vue-router'

const routes = [
    {
        path: '/login',
        name: 'Login',
        component: () => import('../views/Login.vue')
    },
    {
        path: '/',
        name: 'Layout',
        component: () => import('../layouts/Layout.vue'),
        meta: { requiresAuth: true },
        children: [
            { path: '/tasks', name: 'Tasks', component: () => import('../views/Tasks.vue') },
            { path: '/tasks/:id', name: 'task.show', component: () => import('../views/TaskView.vue') }
        ]
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach(async (to, from, next) => {
    const token = localStorage.getItem('token')

    if (to.meta.requiresAuth) {
        if (!token) {
            next({ name: 'Login' })
        } else {
            try {
                await axios.get('/api/me', {
                    headers: { Authorization: `Bearer ${token}` }
                })
                next()
            } catch {
                localStorage.removeItem('token')
                next({ name: 'Login' })
            }
        }
    } else {
        next()
    }
})

export default router
