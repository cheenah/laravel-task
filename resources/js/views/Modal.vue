<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">{{ title }}</h3>
                <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <div class="p-6">
                <slot></slot>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end space-x-3">
                <slot name="footer"></slot>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    isOpen: Boolean,
    title: String
})
defineEmits(['close'])
</script>
