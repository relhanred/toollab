<script setup>
import { ref, onMounted, defineProps, defineEmits } from 'vue'
import apiClient from '~/services/api.js'
import Trash from "~/components/Icons/Trash.vue"

const props = defineProps({
  schoolId: {
    type: Number,
    required: true
  }
})

const emit = defineEmits(['update'])

const users = ref([])
const isLoading = ref(true)
const message = ref({ type: '', text: '' })
const showConfirmModal = ref(false)
const selectedUserForRemoval = ref(null)

const roleLabels = {
  'Director': 'Directeur',
  'Admin': 'Administrateur',
  'Registar': 'Responsable des inscriptions',
  'Teacher': 'Enseignant',
  'Student': 'Élève',
  'Responsible': 'Responsable'
}

const fetchUsers = async () => {
  try {
    isLoading.value = true
    const response = await apiClient.get(`/api/users/school/${props.schoolId}`)

    users.value = response.data.filter(item =>
        ['Director', 'Admin', 'Registar'].includes(item.role)
    )
  } catch (error) {
    console.error('Erreur lors de la récupération des utilisateurs:', error)
    message.value = {
      type: 'error',
      text: 'Une erreur est survenue lors de la récupération des utilisateurs'
    }
  } finally {
    isLoading.value = false
  }
}

const openRemoveRoleModal = (userId, roleName) => {
  selectedUserForRemoval.value = { userId, roleName };
  showConfirmModal.value = true;
}

const closeConfirmModal = () => {
  showConfirmModal.value = false;
  selectedUserForRemoval.value = null;
}

const removeRole = async () => {
  if (!selectedUserForRemoval.value) return;

  try {
    await apiClient.post('/api/users/remove-role', {
      user_id: selectedUserForRemoval.value.userId,
      school_id: props.schoolId,
      role_name: selectedUserForRemoval.value.roleName
    });

    await fetchUsers();
    emit('update');

    const { setFlashMessage } = useFlashMessage();
    setFlashMessage({
      type: 'success',
      message: 'Le rôle de l\'utilisateur a été supprimé avec succès'
    });
  } catch (error) {
    console.error('Erreur lors de la suppression du rôle:', error);
    message.value = {
      type: 'error',
      text: error.response?.data?.message || 'Une erreur est survenue lors de la suppression du rôle'
    };
  } finally {
    closeConfirmModal();
  }
}

onMounted(() => {
  fetchUsers()
})

defineExpose({
  refreshUsers: fetchUsers
})
</script>

<template>
  <div>
    <div
        v-if="message.text"
        :class="[
      'p-4 mb-6 rounded text-white',
      message.type === 'success' ? 'bg-green-500' : 'bg-red-500'
    ]"
    >
      {{ message.text }}
    </div>

    <div v-if="isLoading" class="flex justify-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
    </div>

    <div v-else-if="users.length === 0" class="py-8 text-center text-gray-500">
      Aucun membre du personnel n'a été ajouté.
    </div>

    <div v-else class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
        <tr>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Nom
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Email
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Rôle
          </th>
          <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
            Actions
          </th>
        </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="user in users" :key="user.user.id">
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-900">
              {{ user.user.first_name }} {{ user.user.last_name }}
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm text-gray-500">
              {{ user.user.email }}
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    :class="{
                      'bg-purple-100 text-purple-800': user.role === 'Director',
                      'bg-blue-100 text-blue-800': user.role === 'Admin',
                      'bg-green-100 text-green-800': user.role === 'Registar'
                    }">
                {{ roleLabels[user.role] || user.role }}
              </span>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <button
                v-if="user.role !== 'Director'"
                @click="openRemoveRoleModal(user.user.id, user.role)"
                class="text-red-600 hover:text-red-900 ml-2 p-1 rounded hover:bg-gray-100"
                title="Supprimer le rôle">
              <Trash class="size-4" />
            </button>
          </td>
        </tr>
        </tbody>
      </table>
    </div>

    <ConfirmationModal
        :is-open="showConfirmModal"
        title="Supprimer le rôle"
        message="Êtes-vous sûr de vouloir supprimer ce rôle pour cet utilisateur ?"
        confirm-button-text="Supprimer"
        @confirm="removeRole"
        @cancel="closeConfirmModal"
    />
  </div>
</template>