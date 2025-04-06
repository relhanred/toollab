import { ref, readonly } from 'vue'
import authService from '~/services/auth'

const user = ref(null)
const isAuthenticated = ref(false)
const isLoading = ref(false)
const error = ref(null)

if (process.client) {
    try {
        user.value = authService.getUser()
        isAuthenticated.value = authService.isAuthenticated()
    } catch (e) {
        localStorage.removeItem('auth.user')
        localStorage.removeItem('auth.token')
    }
}

export function useAuth() {
    async function login(credentials) {
        isLoading.value = true
        error.value = null

        try {
            const response = await authService.login(credentials)
            user.value = response.user
            isAuthenticated.value = true
            return response
        } catch (err) {
            error.value = err.response?.data?.message || 'Une erreur est survenue lors de la connexion'
            throw err
        } finally {
            isLoading.value = false
        }
    }

    async function logout() {
        isLoading.value = true
        error.value = null

        try {
            await authService.logout()
            user.value = null
            isAuthenticated.value = false
        } catch (err) {
            error.value = 'Une erreur est survenue lors de la déconnexion'
        } finally {
            isLoading.value = false
        }
    }

    async function register(userData) {
        isLoading.value = true
        error.value = null

        try {
            const response = await authService.register(userData)
            user.value = response.user
            isAuthenticated.value = true
            return response
        } catch (err) {
            error.value = err.response?.data?.message || 'Une erreur est survenue lors de l\'inscription'
            throw err
        } finally {
            isLoading.value = false
        }
    }

    return {
        user: readonly(user),
        isAuthenticated: readonly(isAuthenticated),
        isLoading: readonly(isLoading),
        error: readonly(error),

        login,
        logout,
        register
    }
}