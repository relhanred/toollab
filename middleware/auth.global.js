export default defineNuxtRouteMiddleware((to) => {
    // Ne rien faire côté serveur
    if (process.server) return

    // Routes publiques accessibles sans authentification
    const publicRoutes = ['/login', '/contact']

    // Vérifier si la route actuelle est une route publique
    if (publicRoutes.some(route => to.path === route)) {
        return // Autorise l'accès aux routes publiques
    }

    // Vérifie si l'utilisateur est authentifié
    const token = localStorage.getItem('auth.token')
    const isAuthenticated = !!token

    // Si l'utilisateur n'est pas authentifié et tente d'accéder à une route protégée
    if (!isAuthenticated) {
        return navigateTo({
            path: '/login',
            query: { redirect: to.fullPath }
        })
    }

    // Si l'utilisateur est authentifié et tente d'accéder à login
    if (isAuthenticated && to.path === '/login') {
        return navigateTo('/')
    }
})