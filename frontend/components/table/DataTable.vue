<!-- frontend/components/DataTable.vue -->
<script setup>
import { ref, computed, watch, defineProps, defineEmits } from 'vue'

const props = defineProps({
  // Colonnes du tableau
  columns: {
    type: Array,
    required: true,
    // Format: [{ key: 'id', label: 'ID', width: '5' }, ...]
    // key: identifiant de la colonne dans les données
    // label: titre affiché dans l'en-tête
    // width: largeur de la colonne (nombre de colonnes dans la grille)
  },
  // Données actuelles du tableau
  items: {
    type: Array,
    required: true
  },
  // Pagination
  pagination: {
    type: Object,
    required: true,
    // Format: { currentPage: 1, totalPages: 10, perPage: 10, total: 100 }
  },
  // Indique si le tableau est en cours de chargement
  loading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['page-change'])

// Pagination
const handlePageChange = (page) => {
  if (page === props.pagination.currentPage) return
  if (page < 1 || page > props.pagination.totalPages) return
  emit('page-change', page)
}

// Pages affichées dans la pagination
const displayedPages = computed(() => {
  const { currentPage, totalPages } = props.pagination

  // Affiche toujours 5 pages au maximum
  const maxPages = 5

  if (totalPages <= maxPages) {
    return Array.from({ length: totalPages }, (_, i) => i + 1)
  }

  // Toujours montrer la première et la dernière page
  let pages = [1]

  let startPage = Math.max(2, currentPage - 1)
  let endPage = Math.min(totalPages - 1, currentPage + 1)

  // Ajuster pour toujours montrer 3 pages au milieu
  if (startPage === 2) {
    endPage = Math.min(totalPages - 1, startPage + 2)
  }
  if (endPage === totalPages - 1) {
    startPage = Math.max(2, endPage - 2)
  }

  // Ajouter des ellipses si nécessaire
  if (startPage > 2) {
    pages.push('...')
  }

  // Ajouter les pages du milieu
  for (let i = startPage; i <= endPage; i++) {
    pages.push(i)
  }

  // Ajouter une ellipse si nécessaire
  if (endPage < totalPages - 1) {
    pages.push('...')
  }

  if (totalPages > 1) {
    pages.push(totalPages)
  }

  return pages
})

// Déterminer si la ligne est la dernière pour l'affichage de la bordure
const isLastRow = (index) => {
  return index === props.items.length - 1
}
</script>

<template>
  <div class="w-full overflow-x-auto bg-white rounded-2xl border">
    <!-- En-tête du tableau -->
    <div class="grid font-bold font-montserrat py-4 px-10 border-b border-[#E6EFF5]"
         :style="`grid-template-columns: repeat(${props.columns.reduce((sum, col) => sum + (parseInt(col.width) || 1), 0)}, minmax(0, 1fr))`">
      <div
          v-for="column in props.columns"
          :key="column.key"
          :class="`col-span-${column.width || 1} inline-flex items-center justify-start`"
      >
        {{ column.label }}
      </div>
    </div>

    <!-- État de chargement -->
    <div v-if="loading" class="py-10 text-center">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-default"></div>
      <p class="mt-2 text-gray-500">Chargement...</p>
    </div>

    <!-- Message si aucune donnée -->
    <div v-else-if="items.length === 0" class="py-10 text-center text-gray-500">
      Aucune donnée disponible
    </div>

    <!-- Contenu du tableau -->
    <template v-else>
      <slot
          v-for="(item, index) in items"
          :key="item.id || index"
          :item="item"
          :index="index"
          :isLastRow="isLastRow(index)"
      >
        <!-- Contenu par défaut si aucun slot n'est fourni -->
        <div
            class="grid font-nunito py-4 px-10 hover:bg-gray-50 transition-colors cursor-pointer"
            :class="{ 'border-b border-[#E6EFF5]': !isLastRow(index) }"
            :style="`grid-template-columns: repeat(${props.columns.reduce((sum, col) => sum + (parseInt(col.width) || 1), 0)}, minmax(0, 1fr))`"
        >
          <template v-for="column in props.columns" :key="`${index}-${column.key}`">
            <div :class="`col-span-${column.width || 1} inline-flex items-center justify-start`">
              {{ item[column.key] }}
            </div>
          </template>
        </div>
      </slot>
    </template>

    <!-- Pagination - toujours affichée -->
    <div class="flex justify-between items-center py-4 px-6 border-t border-[#E6EFF5]">
      <!-- Nombre total d'éléments -->
      <div class="text-sm text-gray-600">
        {{ pagination.total }} élément{{ pagination.total > 1 ? 's' : '' }}
      </div>

      <div class="flex items-center space-x-1">
        <!-- Bouton précédent -->
        <button
            @click="handlePageChange(pagination.currentPage - 1)"
            :disabled="pagination.currentPage === 1"
            class="px-3 py-1 rounded-md border"
            :class="pagination.currentPage === 1 ? 'text-gray-400 cursor-not-allowed' : 'hover:bg-gray-100'"
        >
          <span class="sr-only">Page précédente</span>
          &laquo;
        </button>

        <!-- Pages -->
        <template v-for="(page, idx) in displayedPages" :key="idx">
          <button
              v-if="page !== '...'"
              @click="handlePageChange(page)"
              class="px-3 py-1 rounded-md"
              :class="page === pagination.currentPage ? 'bg-default text-white' : 'hover:bg-gray-100'"
          >
            {{ page }}
          </button>
          <span v-else class="px-2 py-1 text-gray-500">...</span>
        </template>

        <!-- Bouton suivant -->
        <button
            @click="handlePageChange(pagination.currentPage + 1)"
            :disabled="pagination.currentPage === pagination.totalPages"
            class="px-3 py-1 rounded-md border"
            :class="pagination.currentPage === pagination.totalPages ? 'text-gray-400 cursor-not-allowed' : 'hover:bg-gray-100'"
        >
          <span class="sr-only">Page suivante</span>
          &raquo;
        </button>
      </div>
    </div>
  </div>
</template>