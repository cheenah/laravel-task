<template>
    <Modal :isOpen="isOpen" title="Изменить статус заявки" @close="$emit('close')">
        <div class="space-y-4">
            <label class="block text-sm font-medium text-gray-700">Выберите новый статус</label>
            <select v-model="localStatus" class="w-full p-2 border rounded">
                <option value="new">Открыта</option>
                <option value="in_progress">В работе</option>
                <option value="done">Решена</option>
                <option value="cancelled">Закрыта</option>
            </select>
        </div>

        <template #footer>
            <button @click="$emit('close')" class="px-4 py-2 text-gray-600">Отмена</button>
            <button @click="submit" :disabled="loading" class="px-4 py-2 bg-blue-600 text-white rounded">
                {{ loading ? 'Обновление...' : 'Сохранить' }}
            </button>
        </template>
    </Modal>
</template>

<script setup>
import { ref, watch } from 'vue'
import Modal from './Modal.vue'

const props = defineProps({
    isOpen: Boolean,
    loading: Boolean,
    currentStatus: String
})
const emit = defineEmits(['close', 'submit'])

const localStatus = ref(props.currentStatus)

watch(() => props.currentStatus, (newVal) => {
    localStatus.value = newVal
})

const submit = () => {
    emit('submit', localStatus.value)
}
</script>
