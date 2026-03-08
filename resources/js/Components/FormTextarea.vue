<script setup>
defineProps({
    label: String,
    modelValue: String,
    error: String,
    name: String,
    rows: {
        type: Number,
        default: 3
    },
    placeholder: String
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
<div class="mb-3">
    <label class="form-label">{{ label }}</label>

    <textarea
        :name="name"
        :rows="rows"
        :placeholder="placeholder"
        class="form-control"
        :class="{ 'is-invalid': error }"
        :value="modelValue"
        @input="emit('update:modelValue', $event.target.value)"
    ></textarea>

    <div v-if="error" class="invalid-feedback">
        {{ error }}
    </div>
</div>
</template>