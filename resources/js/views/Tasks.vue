<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const tasks = ref([])
const loading = ref(true)
const error = ref(null)
const pagination = ref({})
const currentPage = ref(1)
const perPage = ref(15)
const statusFilter = ref('')
const searchQuery = ref('')

const fetchTasks = async (page = 1) => {
    loading.value = true
    try {
        const params = {
            page: page,
            per_page: perPage.value
        }

        if (statusFilter.value) {
            params.status = statusFilter.value
        }

        if (searchQuery.value) {
            params.search = searchQuery.value
        }

        const response = await axios.get('/api/tasks', { params })
        console.log(tasks);
        tasks.value = response.data.data
        pagination.value = response.data
        currentPage.value = response.data.current_page

    } catch (e) {
        console.error('Ошибка загрузки задач:', e)
        error.value = 'Не удалось загрузить задачи. Попробуйте позже.'
    } finally {
        loading.value = false
    }
}

const changePage = (page) => {
    fetchTasks(page)
}

const applyFilters = () => {
    fetchTasks(1)
}

const clearFilters = () => {
    statusFilter.value = ''
    searchQuery.value = ''
    fetchTasks(1)
}

onMounted(() => {
    fetchTasks()
})
</script>

<template>
    <div class="p-8">
        <h1 class="text-2xl font-bold mb-6">Мои заявки</h1>

        <div class="mb-6 p-4 bg-gray-50 rounded-lg space-y-4 md:space-y-0 md:flex md:space-x-4">
            <div class="flex-1">
                <input v-model="searchQuery"
                       @keyup.enter="applyFilters"
                       placeholder="Поиск по заголовку..."
                       class="w-full p-2 border rounded" />
            </div>
            <div>
                <select v-model="statusFilter" class="p-2 border rounded">
                    <option value="">Все статусы</option>
                    <option value="new">Открыта</option>
                    <option value="in_progress">В работе</option>
                    <option value="done">Решена</option>
                    <option value="cancelled">Закрыта</option>
                </select>
            </div>
            <button @click="applyFilters" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Применить
            </button>
            <button @click="clearFilters" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                Сбросить
            </button>
        </div>

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
                    <td class="p-4 text-sm">{{ task.author_id || '—' }}</td>
                    <td class="p-4 text-sm">{{ task.title }}</td>
                    <td class="p-4 text-sm">{{ task.description }}</td>
                    <td class="p-4">
                            <span class="px-2 py-1 text-xs rounded font-medium"
                                  :class="{
                                    'bg-blue-100 text-blue-800': task.status === 'new',
                                    'bg-yellow-100 text-yellow-800': task.status === 'in_progress',
                                    'bg-green-100 text-green-800': task.status === 'done',
                                    'bg-gray-100 text-gray-800': task.status === 'cancelled'
                                  }">
                                {{
                                    task.status === 'new' ? 'Открыта' :
                                        task.status === 'in_progress' ? 'В работе' :
                                            task.status === 'done' ? 'Решена' :
                                                task.status === 'cancelled' ? 'Закрыта' : task.status
                                }}
                            </span>
                    </td>
                    <td class="p-4 text-sm text-gray-500">
                        {{ new Date(task.created_at).toLocaleDateString() }}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div v-if="pagination && pagination.total > perPage" class="mt-6 flex justify-center items-center space-x-2">
            <button @click="changePage(pagination.current_page - 1)"
                    :disabled="!pagination.prev_page_url"
                    class="px-3 py-1 border rounded disabled:opacity-50">
                ← Назад
            </button>

            <span class="text-sm">
                Страница {{ pagination.current_page }} из {{ pagination.last_page }}
            </span>

            <button @click="changePage(pagination.current_page + 1)"
                    :disabled="!pagination.next_page_url"
                    class="px-3 py-1 border rounded disabled:opacity-50">
                Вперед →
            </button>
        </div>

        <div v-if="pagination.total !== undefined" class="mt-4 text-sm text-gray-500 text-center">
            Показано {{ pagination.from }} - {{ pagination.to }} из {{ pagination.total }} записей
        </div>

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
