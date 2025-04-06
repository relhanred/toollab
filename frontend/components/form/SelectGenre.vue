//CustomSelect.vue
<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'

const options = [
  { name: 'Hommes', color: '#93C5FD' },
  { name: 'Femmes', color: '#FDA4AF' },
  { name: 'Enfants', color: '#FCD34D' },
  { name: 'Mixte', color: '#86EFAC' },
]

defineProps({
  placeholder: {
    type: String,
    default: 'Genre'
  }
})

const model = defineModel()
const isOpen = ref(false)
const dropdownRef = ref(null)

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const selectOption = (option) => {
  model.value = option.name
  isOpen.value = false
}

const getSelectedColor = () => {
  const selectedOption = options.find(opt => opt.name === model.value)
  return selectedOption?.color
}

const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
  <div class="relative w-full" ref="dropdownRef">
    <div class="relative">
      <input
          type="text"
          :class="[
      'bg-white placeholder:text-placeholder border rounded-lg focus:outline-default border-placeholder font-nunito placeholder:font-semibold placeholder:text-lg py-3 pr-10 text-default w-full cursor-pointer',
      model ? 'pl-12' : 'pl-4'
    ]"
          :placeholder="placeholder"
          :value="model"
          @click="toggleDropdown"
          readonly
      />
      <div
          v-if="model"
          class="absolute left-4 top-1/2 -translate-y-1/2 flex items-center gap-3"
      >
        <div
            class="size-4 rounded"
            :style="{ backgroundColor: getSelectedColor() }"
        />
      </div>

      <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
        <svg
            class="size-5 text-placeholder"
            :class="{ 'rotate-180': isOpen }"
            viewBox="0 0 12 12"
            fill="currentColor"
        >
          <path d="M2 4 L6 8 L10 4 Z" />
        </svg>
      </div>
    </div>

    <div
        v-if="isOpen"
        class="absolute w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto z-50"
    >
      <div
          v-for="(option, index) in options"
          :key="index"
          class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-default font-nunito flex items-center gap-3"
          @click="selectOption(option)"
      >
        <div
            class="size-4 rounded flex-shrink-0 text-default"
            :style="{ backgroundColor: option.color }"
        />
        {{ option.name }}
      </div>
    </div>
  </div>
</template>