import axios from 'axios'
import { useAuthStore } from '~/store/auth'

export default defineNuxtPlugin(({ $i18n, $swal, $config, $router, provide }) => {
    const authStore = useAuthStore()
    axios.defaults.baseURL = $config.public.apiUrl

    if ($config.public.isStateful) {
        axios.defaults.withCredentials = true
    }

    // Request interceptor
    axios.interceptors.request.use((request) => {
        const token = authStore.token
        if (token) {
            request.headers.Authorization = `Bearer ${token}`
        }

        const locale = $i18n.getLocaleCookie()
        if (locale) {
            request.headers['Accept-Language'] = locale
        }

        if (import.meta.server && $config.public.isStateful && !$config.headers.referer) {
            request.headers.Referer = process.env.SPA_URL
        }

        return request
    })

    // Response interceptor
    axios.interceptors.response.use(response => response, (error) => {
        if (!error.response) {
            return
        }

        const status = error.response.status
        const auth = authStore.check
        const currentPath = $router.currentRoute.fullPath

        if (status === 401) {
            authStore.clear()
            return navigateTo({ name: 'login', query: { redirect: currentPath } })
        }

        if (status === 403) {
            $swal.warning({
                title: $i18n.t('alerts.unauthorized'),
                text: error.response.data.message,
            }).then(() => {
                navigateTo(auth ? { name: 'dashboard' } : '/')
            })
        }

        if (status === 423) {
            return navigateTo({ name: 'password.confirm', query: { redirect: currentPath } })
        }

        return Promise.reject(error)
    })

    for (const method of ['request', 'delete', 'get', 'head', 'options', 'post', 'put', 'patch']) {
        axios['$' + method] = function () { return axios[method].apply(this, arguments).then(res => res && res.data) }
    }

    provide('axios', axios)
})
