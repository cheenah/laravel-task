<template>
    <div class="p-8 max-w-4xl mx-auto ">
        <div class="flex justify-between items-center">
            <button @click="$router.back()" class="mb-4 text-blue-600 hover:underline flex items-center">
                <span class="mr-1">←</span> Назад к списку
            </button>
            <button
                v-if="canEdit && !isEditing"
                @click="isEditing = true"
                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-semibold"
            >
                Редактировать
            </button>

        </div>
        <div v-if="loading" class="text-center py-10 text-gray-500">Загрузка данных...</div>

        <div v-else-if="task" class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-start">
                <div>
                    <div class="flex-1 mr-4">
                        <input
                            v-if="isEditing"
                            v-model="editForm.title"
                            class="text-3xl font-bold text-gray-900 border-b-2 border-blue-500 outline-none w-full mb-2 bg-transparent"
                        />
                        <h1 v-else class="text-3xl font-bold text-gray-900">{{ task.title }}</h1>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">
                        ID: #{{ task.id }} | Создана: {{ new Date(task.created_at).toLocaleString() }}
                    </p>
                    <p class="text-sm text-gray-500">Автор: {{ task.user?.name }}</p>
                </div>

                <div class="flex flex-col items-end">
                    <label class="text-[10px] text-gray-400 uppercase font-bold mb-1">Статус заявки</label>
                    <select
                        v-if="canChangeStatus"
                        :value="task.status"
                        @change="handleStatusChangeAttempt"
                        :disabled="isUpdatingStatus"
                        class="px-4 py-2 rounded-lg text-sm font-semibold border focus:ring-2 focus:ring-blue-500 cursor-pointer outline-none transition-all shadow-sm"
                        :class="statusClass"
                    >
                        <option value="new">Открыта</option>
                        <option value="in_progress">В работе</option>
                        <option value="done">Решена</option>
                        <option value="cancelled">Закрыта</option>
                    </select>
                    <span
                        v-else
                        :class="statusClass"
                        class="px-4 py-2 rounded-lg text-sm font-semibold border shadow-sm"
                    >
                        {{ formattedStatus }}
                    </span>
                    <span v-if="isUpdatingStatus"
                          class="text-[10px] text-blue-500 mt-1 animate-pulse">Сохранение...</span>
                </div>
            </div>

            <div class="p-6">
                <h2 class="text-sm font-bold text-gray-400 uppercase mb-3 tracking-wider">Описание заявки</h2>
                <div v-if="isEditing">
        <textarea
            v-model="editForm.description"
            rows="6"
            class="w-full p-4 rounded-lg border border-blue-300 outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-700"
        ></textarea>
                    <div class="mt-4 flex justify-end space-x-3">
                        <button @click="isEditing = false" class="px-4 py-2 text-gray-500 hover:text-gray-700">Отмена
                        </button>
                        <button @click="saveTask"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-md">
                            Сохранить
                        </button>
                    </div>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100" v-else>
                    <p class="text-gray-700 break-words whitespace-normal leading-relaxed">
                        {{ task.description || 'Описание не заполнено' }}</p>
                </div>
                <div class="px-6 pb-6">
                    <TaskComments
                        :taskId="task.id"
                        :comments="task.comments"
                        @commentAdded="fetchTask"
                    />
                </div>
            </div>
        </div>

        <Modal
            :isOpen="isConfirmModalOpen"
            title="Смена статуса"
            @close="isConfirmModalOpen = false"
        >
            <div class="py-2">
                <p class="text-gray-700 text-lg">Вы действительно хотите перевести заявку в статус
                    <span class="font-bold text-blue-600">"{{ formatStatus(pendingStatus) }}"</span>?
                </p>
            </div>

            <template #footer>
                <div class="flex justify-end space-x-3">
                    <button @click="isConfirmModalOpen = false"
                            class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium">
                        Отмена
                    </button>
                    <button @click="confirmStatusChange"
                            class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-md">
                        Подтвердить
                    </button>
                </div>
            </template>
        </Modal>
    </div>
</template>

<script setup>
import {ref, inject, onMounted, computed, reactive} from 'vue'
import {useRoute} from 'vue-router'
import axios from 'axios'
import Modal from "./Modal.vue"
import TaskComments from './Comments.vue'

const route = useRoute()
const task = ref(null)
const loading = ref(true)

const me = inject('me')
const canChangeStatus = computed(() =>
    me.value?.roles?.includes('admin') || me.value?.roles?.includes('manager')
)

const isConfirmModalOpen = ref(false)
const isUpdatingStatus = ref(false)
const pendingStatus = ref('')


const fetchTask = async () => {
    try {
        const response = await axios.get(`/api/tasks/${route.params.id}`)
        task.value = response.data
        editForm.title = response.data.title
        editForm.description = response.data.description
    } catch (e) {
        console.error('Ошибка:', e)
    } finally {
        loading.value = false
    }
}
const statusLabels = {
    new: 'Открыта',
    in_progress: 'В работе',
    done: 'Решена',
    cancelled: 'Закрыта'
}

const formattedStatus = computed(() => {
    return statusLabels[task.value?.status] || task.value?.status
})


const handleStatusChangeAttempt = (event) => {
    pendingStatus.value = event.target.value
    isConfirmModalOpen.value = true
}

const confirmStatusChange = async () => {
    isConfirmModalOpen.value = false
    isUpdatingStatus.value = true
    try {
        await axios.put(`/api/tasks/${route.params.id}/status`, {
            status: pendingStatus.value
        })
        task.value.status = pendingStatus.value
    } catch (e) {
        alert('Не удалось изменить статус.')
    } finally {
        isUpdatingStatus.value = false
    }
}

const statusClass = computed(() => {
    const status = task.value?.status
    return {
        'bg-blue-50 text-blue-700 border-blue-200': status === 'new',
        'bg-yellow-50 text-yellow-700 border-yellow-200': status === 'in_progress',
        'bg-green-50 text-green-700 border-green-200': status === 'done',
        'bg-gray-50 text-gray-700 border-gray-200': status === 'cancelled',
    }
})

const formatStatus = (status) => statusLabels[status] || status

const isEditing = ref(false)
const editForm = reactive({title: '', description: ''})
const canEdit = computed(() => {
    const roles = me.value?.roles || []
    const isAdmin = roles.includes('admin')
    const isAuthorAndNew = task.value?.author_id === me.value?.id && task.value?.status === 'new'
    return isAdmin || isAuthorAndNew
})
const saveTask = async () => {
    try {
        await axios.put(`/api/tasks/${route.params.id}`, editForm)
        task.value.title = editForm.title
        task.value.description = editForm.description
        isEditing.value = false
    } catch (e) {
        alert('Не удалось сохранить изменения')
    }
}

onMounted(fetchTask)
</script>
