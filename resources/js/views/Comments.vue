<template>
    <div class="mt-8 border-t pt-6">
        <h2 class="text-lg font-semibold mb-4 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            Комментарии ({{ comments.length }})
        </h2>

        <div class="space-y-4 mb-6">
            <div v-for="comment in comments" :key="comment.id" class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-bold text-sm text-blue-600">{{ comment.author?.name || 'Система' }}</span>
                    <span class="text-xs text-gray-400">{{ formatDate(comment.created_at) }}</span>
                </div>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ comment.content }}</p>
            </div>

            <div v-if="comments.length === 0" class="text-center py-4 text-gray-400 italic text-sm">
                Здесь пока нет комментариев. Будьте первым!
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
            <textarea
                v-model="newComment"
                rows="3"
                class="w-full p-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                placeholder="Напишите уточнение или ответ..."
            ></textarea>
            <div class="mt-2 flex justify-end">
                <button
                    @click="submitComment"
                    :disabled="!newComment.trim() || isSending"
                    class="px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors shadow-sm"
                >
                    {{ isSending ? 'Отправка...' : 'Отправить комментарий' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const props = defineProps({
    taskId: { type: [Number, String], required: true },
    comments: { type: Array, default: () => [] }
})

const emit = defineEmits(['commentAdded'])

const newComment = ref('')
const isSending = ref(false)

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString('ru-RU', {
        day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'
    })
}

const submitComment = async () => {
    if (!newComment.value.trim()) return
    isSending.value = true
    try {
        const response = await axios.post(`/api/tasks/${props.taskId}/comments`, {
            content: newComment.value
        })
        newComment.value = ''
        emit('commentAdded', response.data)
    } catch (e) {
        alert('Ошибка при отправке комментария')
    } finally {
        isSending.value = false
    }
}
</script>
