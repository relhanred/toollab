<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuth } from '~/composables/useAuth'
import InputText from "~/components/form/InputText.vue"
import SaveButton from "~/components/form/SaveButton.vue"
import apiClient from '~/services/api'
import { usePageTitle } from "~/composables/usePageTitle.js"

definePageMeta({
  layout: 'auth',
  layoutData: {
    title: 'Paramètres'
  }
})

usePageTitle('Paramètres')

const { user } = useAuth()
const { setFlashMessage } = useFlashMessage()
const isDirector = ref(false)
const activeTab = ref('profile')
const isLoading = ref(true)
const message = ref({ type: '', text: '' })
const school = ref(null)
const logoFile = ref(null)
const logoPreview = ref('')

const userForm = ref({
  first_name: '',
  last_name: ''
})

const passwordForm = ref({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const schoolForm = ref({
  name: '',
  email: '',
  phone: '',
  address: '',
  zipcode: '',
  city: '',
  country: ''
})

const checkIfDirector = async () => {
  try {
    const response = await apiClient.get(`/api/users/${user.value.id}/roles`)
    const roles = response.data.roles

    if (roles.schools && roles.schools.length) {
      const directorRole = roles.schools.find(role => role.role === 'Director')
      if (directorRole) {
        isDirector.value = true
        const schoolResponse = await apiClient.get(`/api/schools/${directorRole.context.id}`)
        school.value = schoolResponse.data
        populateSchoolForm()
      }
    }
  } catch (error) {
    console.error('Erreur lors de la vérification du rôle:', error)
  } finally {
    isLoading.value = false
  }
}

const populateUserForm = () => {
  if (user.value) {
    userForm.value.first_name = user.value.first_name || ''
    userForm.value.last_name = user.value.last_name || ''
  }
}

const populateSchoolForm = () => {
  if (school.value) {
    schoolForm.value.name = school.value.name || ''
    schoolForm.value.email = school.value.email || ''
    schoolForm.value.phone = school.value.phone || ''
    schoolForm.value.address = school.value.address || ''
    schoolForm.value.zipcode = school.value.zipcode || ''
    schoolForm.value.city = school.value.city || ''
    schoolForm.value.country = school.value.country || ''

    if (school.value.logo) {
      logoPreview.value = `${useRuntimeConfig().public.apiUrl}/storage/${school.value.logo}`
    }
  }
}


const handleUpdateUser = async () => {
  try {
    message.value = { type: '', text: '' }

    const response = await apiClient.put(`/api/users/${user.value.id}`, {
      first_name: userForm.value.first_name,
      last_name: userForm.value.last_name,
      email: user.value.email
    })

    const userData = JSON.parse(localStorage.getItem('auth.user'))
    userData.first_name = userForm.value.first_name
    userData.last_name = userForm.value.last_name
    localStorage.setItem('auth.user', JSON.stringify(userData))

    // Remplacer le message local par un flash message
    setFlashMessage({
      type: 'success',
      message: 'Informations mises à jour avec succès'
    })

    user.value = userData

  } catch (error) {
    console.error('Erreur lors de la mise à jour du profil:', error)
    message.value = {
      type: 'error',
      text: error.response?.data?.message || 'Une erreur est survenue lors de la mise à jour'
    }
  }
}

const handleUpdatePassword = async () => {
  try {
    message.value = { type: '', text: '' }

    if (passwordForm.value.password !== passwordForm.value.password_confirmation) {
      message.value = { type: 'error', text: 'Les mots de passe ne correspondent pas' }
      return
    }

    await apiClient.post(`/api/users/change-password`, {
      current_password: passwordForm.value.current_password,
      password: passwordForm.value.password,
      password_confirmation: passwordForm.value.password_confirmation
    })

    // Remplacer le message local par un flash message
    setFlashMessage({
      type: 'success',
      message: 'Mot de passe mis à jour avec succès'
    })

    passwordForm.value = {
      current_password: '',
      password: '',
      password_confirmation: ''
    }

  } catch (error) {
    console.error('Erreur lors de la mise à jour du mot de passe:', error)
    message.value = {
      type: 'error',
      text: error.response?.data?.message || 'Une erreur est survenue lors de la mise à jour du mot de passe'
    }
  }
}

const handleUpdateSchool = async () => {
  try {
    message.value = { type: '', text: '' }

    const formData = new FormData()

    // Ajouter cette ligne pour simuler une requête PUT avec POST
    formData.append('_method', 'PUT')

    // Assurez-vous que tous les champs sont inclus, même vides
    Object.keys(schoolForm.value).forEach(key => {
      formData.append(key, schoolForm.value[key] || '')
    })

    if (logoFile.value) {
      formData.append('logo', logoFile.value)
    }

    // Changez PUT en POST
    const response = await apiClient.post(`/api/schools/${school.value.id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    school.value = response.data

    if (response.data.logo) {
      logoPreview.value = `${useRuntimeConfig().public.apiUrl}/storage/${response.data.logo}`
    }

    // Remplacer le message local par un flash message
    setFlashMessage({
      type: 'success',
      message: 'Informations de l\'école mises à jour avec succès'
    })

  } catch (error) {
    console.error('Erreur lors de la mise à jour de l\'école:', error)
    message.value = {
      type: 'error',
      text: error.response?.data?.message || 'Une erreur est survenue lors de la mise à jour de l\'école'
    }
  }
}

const handleFileChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    logoFile.value = file
    logoPreview.value = URL.createObjectURL(file)
  }
}

onMounted(async () => {
  populateUserForm()
  await checkIfDirector()
})
</script>

<template>
  <div class="container mx-auto px-6 py-8">
    <div v-if="isLoading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
    </div>

    <div v-else class="bg-white shadow rounded-lg">
      <div class="flex border-b">
        <button
            @click="activeTab = 'profile'"
            :class="[
            'px-6 py-3 font-medium text-sm focus:outline-none transition-colors',
            activeTab === 'profile'
              ? 'border-b-2 border-black text-black font-semibold'
              : 'text-gray-500 hover:text-black'
          ]"
        >
          Profil
        </button>

        <button
            @click="activeTab = 'password'"
            :class="[
            'px-6 py-3 font-medium text-sm focus:outline-none transition-colors',
            activeTab === 'password'
              ? 'border-b-2 border-black text-black font-semibold'
              : 'text-gray-500 hover:text-black'
          ]"
        >
          Mot de passe
        </button>

        <button
            v-if="isDirector"
            @click="activeTab = 'school'"
            :class="[
            'px-6 py-3 font-medium text-sm focus:outline-none transition-colors',
            activeTab === 'school'
              ? 'border-b-2 border-black text-black font-semibold'
              : 'text-gray-500 hover:text-black'
          ]"
        >
          Mon établissement
        </button>
      </div>

      <div class="p-6">
        <div
            v-if="message.text"
            :class="[
            'p-4 mb-6 rounded text-white',
            message.type === 'success' ? 'bg-green-500' : 'bg-red-500'
          ]"
        >
          {{ message.text }}
        </div>

        <div v-if="activeTab === 'profile'">
          <h2 class="text-lg font-semibold mb-6">Informations personnelles</h2>

          <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
              <InputText
                  v-model="userForm.first_name"
                  placeholder="Prénom"
              />
            </div>

            <div>
              <InputText
                  v-model="userForm.last_name"
                  placeholder="Nom"
              />
            </div>
          </div>

          <div class="flex justify-end mt-6">
            <SaveButton @click="handleUpdateUser">
              Enregistrer
            </SaveButton>
          </div>
        </div>

        <div v-if="activeTab === 'password'">
          <h2 class="text-lg font-semibold mb-6">Changer de mot de passe</h2>

          <div class="grid grid-cols-1 gap-6">
            <div>
              <InputText
                  v-model="passwordForm.current_password"
                  placeholder="Mot de passe actuel"
                  type="password"
              />
            </div>

            <div>
              <InputText
                  v-model="passwordForm.password"
                  placeholder="Nouveau mot de passe"
                  type="password"
              />
            </div>

            <div>
              <InputText
                  v-model="passwordForm.password_confirmation"
                  placeholder="Confirmer le nouveau mot de passe"
                  type="password"
              />
            </div>
          </div>

          <div class="flex justify-end mt-6">
            <SaveButton @click="handleUpdatePassword">
              Mettre à jour
            </SaveButton>
          </div>
        </div>

        <div v-if="activeTab === 'school' && isDirector">
          <h2 class="text-lg font-semibold mb-6">Informations de l'établissement</h2>

          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Logo de l'établissement
            </label>

            <div class="flex items-center space-x-6">
              <div
                  v-if="logoPreview"
                  class="w-24 h-24 rounded-full overflow-hidden border"
              >
                <img
                    :src="logoPreview"
                    alt="Logo de l'école"
                    class="w-full h-full object-cover"
                />
              </div>

              <div
                  v-else
                  class="w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center border"
              >
                <span class="text-gray-400">Aucun logo</span>
              </div>

              <label class="cursor-pointer bg-gray-50 px-4 py-2 border rounded hover:bg-gray-100 transition-colors">
                <span>{{ logoPreview ? 'Changer le logo' : 'Ajouter un logo' }}</span>
                <input
                    type="file"
                    accept="image/*"
                    class="hidden"
                    @change="handleFileChange"
                />
              </label>
            </div>
          </div>

          <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
              <InputText
                  v-model="schoolForm.name"
                  placeholder="Nom de l'établissement"
              />
            </div>

            <div>
              <InputText
                  v-model="schoolForm.email"
                  placeholder="Email de l'établissement"
                  type="email"
              />
            </div>

            <div>
              <InputText
                  v-model="schoolForm.phone"
                  placeholder="Téléphone"
              />
            </div>

            <div class="md:col-span-2">
              <InputText
                  v-model="schoolForm.address"
                  placeholder="Adresse"
              />
            </div>

            <div>
              <InputText
                  v-model="schoolForm.zipcode"
                  placeholder="Code postal"
              />
            </div>

            <div>
              <InputText
                  v-model="schoolForm.city"
                  placeholder="Ville"
              />
            </div>

            <div>
              <InputText
                  v-model="schoolForm.country"
                  placeholder="Pays"
              />
            </div>
          </div>

          <div class="flex justify-end mt-6">
            <SaveButton @click="handleUpdateSchool">
              Enregistrer
            </SaveButton>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>