<script setup>
import { useAttrs } from 'vue'

defineOptions({
  inheritAttrs: false
})

const attrs = useAttrs()

defineProps({
    label: String,
    modelValue: String,
    error: String,
    type: {
        type: String,
        default: 'text'
    },
    name: String,
    placeholder: String
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
    <div class="mb-3">

        <label class="form-label">{{ label }}</label>

        <input
            v-bind="$attrs"
            :type="type"
            :name="name"
            :value="modelValue"
            :placeholder="placeholder"
            class="form-control"
            :class="{ 'is-invalid': error }"
            @input="emit('update:modelValue', $event.target.value)"
        >

        <div v-if="error" class="invalid-feedback">
            {{ error }}
        </div>

    </div>
</template>