<script setup>
import { defineModel } from 'vue'

const options = [
  { value: 'levels', label: 'Par niveaux' },
  { value: 'continu', label: 'Continu' }
]

const modelValue = defineModel({
  default: 'levels'
})
</script>

<template>
  <div class="inline-flex p-1.5 bg-neutral-100 border border-neutral-200 rounded-full w-fit font-nunito font-semibold">
    <button
        v-for="option in options"
        :key="option.value"
        @click="modelValue = option.value"
        :class="[
        'px-10 py-2.5 rounded-full transition-all font-bold duration-150 ease-in-out min-w-[200px] text-center ',
        modelValue === option.value
          ? 'bg-white text-default shadow-xl border border-default'
          : ' text-default hover:opacity-90'
      ]"
    >
      {{ option.label }}
    </button>
  </div>
</template>