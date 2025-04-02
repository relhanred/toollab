import { useApi } from './api'

export default {
    /**
     * Authentifie un utilisateur avec son email et mot de passe
     * @param {Object} credentials - Identifiants de connexion
     * @param {string} credentials.email - Email de l'utilisateur
     * @param {string} credentials.password - Mot de passe
     * @param {boolean} credentials.remember - Se souvenir de moi
     * @returns {Promise} Promesse résolue avec les données de l'utilisateur
     */
    async login(credentials) {
        try {
            const axios = useApi()
            const response = await axios.post('/api/login', credentials)

            if (response.data && response.data.token) {
                // Stockage du token et des informations utilisateur
                localStorage.setItem('auth.token', response.data.token)
                localStorage.setItem('auth.user', JSON.stringify(response.data.user))

                // Mise à jour des en-têtes pour les futures requêtes
                axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`
            }

            return response.data
        } catch (error) {
            throw error
        }
    },

    /**
     * Déconnecte l'utilisateur
     * @returns {Promise} Promesse résolue après la déconnexion
     */
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

    /**
     * Récupère les informations de l'utilisateur connecté
     * @returns {Object|null} Informations de l'utilisateur ou null
     */
    getUser() {
        if (process.client) {
            const userJson = localStorage.getItem('auth.user')
            return userJson ? JSON.parse(userJson) : null
        }
        return null
    },

    /**
     * Vérifie si l'utilisateur est connecté
     * @returns {boolean} True si l'utilisateur est connecté
     */
    isAuthenticated() {
        if (process.client) {
            return !!localStorage.getItem('auth.token')
        }
        return false
    },

    /**
     * Enregistre un nouvel utilisateur
     * @param {Object} userData - Données d'inscription
     * @returns {Promise} Promesse résolue avec les données de l'utilisateur
     */
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