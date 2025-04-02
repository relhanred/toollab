import { useApi } from './api'

export default {

    async login(credentials) {
        try {
            const axios = useApi()
            const response = await axios.post('/api/login', credentials)

            if (response.data && response.data.token) {
                localStorage.setItem('auth.token', response.data.token)
                localStorage.setItem('auth.user', JSON.stringify(response.data.user))

                axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`
            }

            return response.data
        } catch (error) {
            throw error
        }
    },

    async logout() {
        const axios = useApi()
        try {
            await axios.post('/api/logout')
        } catch (error) {
            console.error('Erreur lors de la déconnexion:', error)
        } finally {
            localStorage.removeItem('auth.token')
            localStorage.removeItem('auth.user')
            delete axios.defaults.headers.common['Authorization']
        }
    },

    getUser() {
        if (process.client) {
            const userJson = localStorage.getItem('auth.user')
            return userJson ? JSON.parse(userJson) : null
        }
        return null
    },

    isAuthenticated() {
        if (process.client) {
            return !!localStorage.getItem('auth.token')
        }
        return false
    },

    async register(userData) {
        const axios = useApi()
        const response = await axios.post('/api/register', userData)

        if (response.data && response.data.token) {
            localStorage.setItem('auth.token', response.data.token)
            localStorage.setItem('auth.user', JSON.stringify(response.data.user))

            axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`
        }

        return response.data
    }
}