import authService from '~/services/auth'
import { setupInterceptors } from '~/services/api'

export default defineNuxtPlugin(nuxtApp => {
    // Configuration des interceptors Axios
    setupInterceptors()

    // Injection du service d'authentification
    return {
        provide: {
            auth: authService
        }
    }
})