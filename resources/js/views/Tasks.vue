<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const tasks = ref([])
const loading = ref(true)
const error = ref(null)

const fetchTasks = async () => {
    // try {
    //     const response = await axios.get('/api/tasks')  // ← твой API-роут для задач
    //     tasks.value = response.data
    // } catch (e) {
    //     console.error('Ошибка загрузки задач:', e)
    //     error.value = 'Не удалось загрузить задачи. Попробуйте позже.'
    // } finally {
    //     loading.value = false
    // }
}

onMounted(fetchTasks)
</script>

<template>
    <div class="p-8">
        <h1 class="text-2xl font-bold mb-6">Мои задачи</h1>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="w-full text-left border-collapse">
                <thead>
                <tr class="bg-gray-50">
                    <th class="p-4 font-bold uppercase text-xs text-gray-500">ID</th>
                    <th class="p-4 font-bold uppercase text-xs text-gray-500">Пользователь</th>
                    <th class="p-4 font-bold uppercase text-xs text-gray-500">Заголовок</th>
                    <th class="p-4 font-bold uppercase text-xs text-gray-500">Сообщение</th>
                    <th class="p-4 font-bold uppercase text-xs text-gray-500">Статус</th>
                    <th class="p-4 font-bold uppercase text-xs text-gray-500">Дата создания</th>
                </tr>
                </thead>
                <tbody class="divide-y">
                <tr v-for="task in tasks" :key="task.id" class="hover:bg-gray-50">
                    <td class="p-4 text-sm">{{ task.id }}</td>
                    <td class="p-4 text-sm">{{ task.user?.name || task.author || '—' }}</td>
                    <td class="p-4 text-sm">{{ task.title }}</td>
                    <td class="p-4 text-sm">{{ task.message }}</td>
                    <td class="p-4">
              <span
                  class="px-2 py-1 text-xs rounded font-medium"
                  :class="{
                  'bg-green-100 text-green-800': task.status === 'completed',
                  'bg-yellow-100 text-yellow-800': task.status === 'in_progress',
                  'bg-blue-100 text-blue-800': task.status === 'new',
                  'bg-red-100 text-red-800': task.status === 'cancelled'
                }"
              >
                {{ task.status }}
              </span>
                    </td>
                    <td class="p-4 text-sm text-gray-500">{{ task.created_at }}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Состояния загрузки и ошибок -->
        <div v-if="loading" class="mt-8 text-center text-gray-500">
            Загрузка задач...
        </div>

        <div v-else-if="error" class="mt-8 p-4 bg-red-50 text-red-700 rounded-lg text-center">
            {{ error }}
        </div>

        <div v-else-if="tasks.length === 0" class="mt-8 text-center text-gray-500">
            Заявок пока нет
        </div>
    </div>
</template>
