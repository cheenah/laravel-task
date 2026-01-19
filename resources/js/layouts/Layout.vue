<template>
    <div class="min-h-screen bg-gray-100">
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-end">
                <button
                    @click="logout"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium
           text-white bg-blue-600 rounded-md
           hover:bg-blue-700
           transition"
                >
                    Logout
                </button>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 py-6">
            <router-view />
        </main>
    </div>
</template>


<script setup>
import { ref, onMounted, provide } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const me = ref(null)
const fetchMe = async () => {
    const { data } = await axios.get('/api/me')
    me.value = data
}
const logout = async () => {
    const token = localStorage.getItem('token')

    if (token) {
        try {
            await axios.post('/api/logout', {}, {
                headers: { Authorization: `Bearer ${token}` }
            })
        } catch {}
    }

    localStorage.removeItem('token')
    router.push({ name: 'Login' })
}
provide('me', me)

onMounted(fetchMe)
</script>
