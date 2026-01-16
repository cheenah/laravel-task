<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const email = ref('')
const password = ref('')
const errorMessage = ref('')

const router = useRouter()

const handleLogin = async () => {
    errorMessage.value = ''

    try {
        const response = await axios.post('/api/auth', {
            email: email.value,
            password: password.value
        })

        const token = response.data.token
        localStorage.setItem('token', token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

        router.push('/tasks')
    } catch (error) {
        if (error.response?.data?.message) {
            errorMessage.value = error.response.data.message
            console.error(error.response.data)
        } else {
            errorMessage.value = 'Произошла ошибка. Попробуйте позже.'
        }
    }
}
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <form @submit.prevent="handleLogin" class="p-8 bg-white shadow-lg rounded-lg w-96">
            <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Вход</h2>

            <div v-if="errorMessage" class="mb-4 p-3 bg-red-100 text-red-700 rounded-md text-center">
                {{ errorMessage }}
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input
                    v-model="email"
                    type="email"
                    required
                    autocomplete="email"
                    class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                />
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700">Пароль</label>
                <input
                    v-model="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                />
            </div>

            <button
                type="submit"
                class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition disabled:opacity-50"
                :disabled="email === '' || password === ''"
            >
                Войти
            </button>
        </form>
    </div>
</template>
