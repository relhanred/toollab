import apiClient from './api'

export default {
    async getCursus(params = {}) {
        try {
            const response = await apiClient.get('/api/cursus', { params })
            return response.data
        } catch (error) {
            console.error('Erreur lors de la récupération des cursus:', error)
            throw error
        }
    },

    async getCursusById(id) {
        try {
            const response = await apiClient.get(`/api/cursus/${id}`)
            console.log(response.data);
            return response.data
        } catch (error) {
            console.error(`Erreur lors de la récupération du cursus ${id}:`, error)
            throw error
        }
    },

    async createCursus(cursusData) {
        try {
            // Ajouter l'ID de l'école si ce n'est pas déjà présent
            const schoolId = localStorage.getItem('current_school_id') || 1
            const payload = {
                ...cursusData,
                school_id: cursusData.school_id || schoolId
            }

            const response = await apiClient.post('/api/cursus', payload)
            return response.data
        } catch (error) {
            console.error('Erreur lors de la création du cursus:', error)
            throw error
        }
    },

    async updateCursus(id, cursusData) {
        try {
            const response = await apiClient.put(`/api/cursus/${id}`, cursusData)
            return response.data
        } catch (error) {
            console.error(`Erreur lors de la mise à jour du cursus ${id}:`, error)
            throw error
        }
    },

    async deleteCursus(id) {
        try {
            const response = await apiClient.delete(`/api/cursus/${id}`)
            return response.data
        } catch (error) {
            console.error(`Erreur lors de la suppression du cursus ${id}:`, error)
            throw error
        }
    },

    async getClassesByCursus(cursusId) {
        try {
            const response = await apiClient.get(`/api/cursus/${cursusId}/classes`)
            return response.data
        } catch (error) {
            console.error(`Erreur lors de la récupération des classes du cursus ${cursusId}:`, error)
            throw error
        }
    }
}