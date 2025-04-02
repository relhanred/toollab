export default defineNuxtRouteMiddleware((to, from) => {
    // Seulement exécuter côté client
    if (process.server) return

    // Vérifier le localStorage de manière sécurisée
    const token = localStorage.getItem('auth.token')
    const isAuthenticated = !!token

    if (!isAuthenticated && to.meta.requiresAuth) {
        return navigateTo({
            path: '/login',
            query: { redirect: to.fullPath }
        })
    }

    if (isAuthenticated && to.path === '/login') {
        return navigateTo('/')
    }
})