<script setup>
defineProps({
    label: String,
    modelValue: [String, Number],
    error: String,
    name: String,
    options: {
        type: Array,
        default: () => []
    },
    placeholder: {
        type: String,
        default: '-Pilih-'
    }
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
<div class="mb-3">
    <label class="form-label">{{ label }}</label>

    <select
        :name="name"
        class="form-select"
        :class="{ 'is-invalid': error }"
        :value="modelValue"
        @change="emit('update:modelValue', $event.target.value)"
    >
        <option value="" disabled>
            {{ placeholder }}
        </option>

        <option
            v-for="option in options"
            :key="option"
            :value="option"
        >
            {{ option }}
        </option>
    </select>

    <div v-if="error" class="invalid-feedback">
        {{ error }}
    </div>
</div>
</template>