<script setup>
import { ref } from 'vue'
import SelectGenre from "~/components/form/SelectGenre.vue"
import SaveButton from "~/components/form/SaveButton.vue"
import CancelButton from "~/components/form/CancelButton.vue"
import Cross from "~/components/Icons/Cross.vue"
import InputText from "~/components/form/InputText.vue"

defineProps({
  isOpen: {
    type: Boolean,
    required: true
  },
  cursusName: {
    type: String,
    default: ''
  },
  levelId: {
    type: Number,
    default: 1
  },
  levels: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['close', 'save'])
const error = ref('')
const isSubmitting = ref(false)

const newClass = ref({
  name: '',
  gender: '',
  size: '',
  levelId: 1
})

const handleSave = () => {
  if (!newClass.value.name || !newClass.value.gender || !newClass.value.size) {
    error.value = 'Tous les champs sont requis'
    return
  }

  try {
    isSubmitting.value = true
    error.value = ''

    const classData = {
      ...newClass.value,
      size: parseInt(newClass.value.size)
    }

    emit('save', classData)

    // Reset form
    newClass.value = {
      name: '',
      gender: '',
      size: '',
      levelId: 1
    }

    // Close modal
    emit('close')
  } catch (err) {
    console.error('Erreur lors de la création de la classe:', err)
    error.value = 'Une erreur est survenue lors de la création de la classe'
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 font-nunito bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl px-8 pt-6 pb-10 w-[60rem] max-h-[90vh] overflow-y-auto">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold mx-auto">Ajouter une classe</h2>
        <button
            @click="$emit('close')"
            class="text-gray-500 hover:text-gray-700 p-2 rounded-full hover:bg-gray-50"
            aria-label="Fermer"
        >
          <Cross class="size-6"/>
        </button>
      </div>
      <div class="w-full h-px border rounded-xl bg-gray-200"></div>

      <div v-if="error" class="bg-red-100 text-red-800 p-3 rounded mt-4 mb-2">
        {{ error }}
      </div>

      <div class="mt-8">
        <div class="grid grid-cols-2 gap-6 mb-4">
          <div class="flex flex-col gap-y-2">
            <div class="text-lg font-bold text-default mb-2 pl-2">Nom de la classe</div>
            <InputText
                v-model="newClass.name"
                placeholder="Nom de la classe"
            />
          </div>

          <div class="flex flex-col gap-y-2">
            <div class="text-lg font-bold text-default mb-2 pl-2">Niveau</div>
            <div class="relative">
              <select
                  v-model="newClass.levelId"
                  class="w-full bg-white placeholder:text-placeholder border rounded-lg focus:outline-default border-placeholder font-nunito placeholder:font-semibold placeholder:text-lg py-3 pl-4 text-default appearance-none pr-10"
              >
                <option v-for="level in levels" :key="level.id" :value="level.id">
                  {{ level.name }}
                </option>
              </select>
              <div class="absolute right-4 top-1/2 transform -translate-y-1/2 pointer-events-none">
                <svg class="size-5 text-placeholder" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-4">
          <div class="flex flex-col gap-y-2">
            <div class="text-lg font-bold text-default mb-2 pl-2">Genre</div>
            <SelectGenre
                v-model="newClass.gender"
                placeholder="Genre"
            />
          </div>

          <div class="flex flex-col gap-y-2">
            <div class="text-lg font-bold text-default mb-2 pl-2">Effectif maximum</div>
            <input
                type="number"
                v-model="newClass.size"
                class="w-full bg-white placeholder:text-placeholder border rounded-lg focus:outline-default border-placeholder font-nunito placeholder:font-semibold placeholder:text-lg py-3 pl-4 text-default"
                placeholder="Effectif maximum"
            />
          </div>
        </div>
      </div>

      <div class="flex justify-center gap-x-3 mt-10">
        <CancelButton @click="$emit('close')" :disabled="isSubmitting">Annuler</CancelButton>
        <SaveButton @click="handleSave" :disabled="isSubmitting">
          {{ isSubmitting ? 'Création en cours...' : 'Enregistrer' }}
        </SaveButton>
      </div>
    </div>
  </div>
</template>