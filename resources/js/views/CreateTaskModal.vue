<template>
    <Modal :isOpen="isOpen" title="Создать новую заявку" @close="$emit('close')">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Заголовок</label>
                <input v-model="form.title" type="text" class="w-full p-2 border rounded mt-1" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Описание</label>
                <textarea v-model="form.description" class="w-full p-2 border rounded mt-1" rows="3"></textarea>
            </div>
        </div>

        <template #footer>
            <button @click="$emit('close')" class="px-4 py-2 text-gray-600">Отмена</button>
            <button @click="submit" :disabled="loading" class="px-4 py-2 bg-green-600 text-white rounded">
                {{ loading ? 'Создание...' : 'Создать' }}
            </button>
        </template>
    </Modal>
</template>

<script setup>
import { ref } from 'vue'
import Modal from './Modal.vue'

defineProps({ isOpen: Boolean, loading: Boolean })
const emit = defineEmits(['close', 'submit'])

const form = ref({ title: '', description: '', status: 'new' })

const submit = () => {
    emit('submit', { ...form.value })
    form.value = { title: '', description: '', status: 'new' }
}
</script>
