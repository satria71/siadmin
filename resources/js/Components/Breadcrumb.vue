<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  items: Array
})
</script>

<template>
  <ol class="breadcrumb breadcrumb-arrows mb-0">
    <li
      v-for="(item, index) in items"
      :key="index"
      class="breadcrumb-item"
      :class="{ active: index === items.length - 1 }"
    >
      <!-- Jika bukan item terakhir → pakai Link -->
      <template v-if="index !== items.length - 1">
        <Link :href="item.url">
          {{ item.label }}
        </Link>
      </template>

      <!-- Jika terakhir → hanya text -->
      <template v-else>
        {{ item.label }}
      </template>
    </li>
  </ol>
</template>