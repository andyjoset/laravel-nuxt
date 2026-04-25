import { useAuthStore } from '~/store/auth'

export default {
    routes: (_routes) => [
        {
            path: '/a',
            name: 'auth',
            meta: { layout: 'auth' },
            redirect: { name: 'login' },
            component: () => import('~/pages/auth/index.vue'),
            children: [
                {
                    path: 'login',
                    name: 'login',
                    meta: { middleware: 'guest' },
                    component: () => import('~/pages/auth/login.vue'),
                },
                {
                    path: 'register',
                    name: 'register',
                    meta: { middleware: 'guest' },
                    component: () => import('~/pages/auth/register.vue'),
                },
                {
                    path: 'confirm-password',
                    name: 'password.confirm',
                    meta: {
                        middleware: [
                            'auth',
                            (to) => {
                                if (!to.query.redirect) {
                                    return navigateTo('/')
                                }
                            },
                        ],
                    },
                    component: () => import('~/pages/auth/confirm-password.vue'),
                },
                {
                    path: 'reset-password/:token',
                    name: 'password.reset',
                    meta: { middleware: 'guest' },
                    component: () => import('~/pages/auth/reset-password.vue'),
                },
                {
                    path: 'email/verify',
                    name: 'email.verify',
                    meta: {
                        middleware: [
                            'auth',
                            (to, from) => {
                                const user = useAuthStore().user
                                if (user.email_verified_at === undefined || user.email_verified_at) {
                                    return navigateTo({ name: 'profile.show' })
                                }
                            },
                        ],
                    },
                    component: () => import('~/pages/auth/verify-email.vue'),
                },
            ]
        },

        {
            name: 'home',
            path: '/',
            component: () => import('~/pages/index.vue'),
        },
        {
            name: 'about',
            path: '/about',
            component: () => import('~/pages/about.vue'),
        },
        {
            name: 'contact',
            path: '/contact',
            component: () => import('~/pages/contact.vue'),
        },
        {
            name: 'dashboard',
            path: '/dashboard',
            meta: { middleware: 'auth' },
            component: () => import('~/pages/dashboard.vue'),
        },

        // Admin routes
        {
            path: '/admin',
            name: 'admin.index',
            meta: { middleware: ['auth', 'permission'] },
            component: () => import('~/pages/admin/index.vue'),
            children: [
                {
                    path: 'users',
                    name: 'admin.users',
                    component: () => import('~/pages/admin/users.vue'),
                    meta: { permission: 'users.index' },
                },
                {
                    path: 'roles',
                    name: 'admin.roles',
                    component: () => import('~/pages/admin/roles.vue'),
                    meta: { permission: 'roles.index' },
                },
            ]
        },

        // Profile routes
        {
            path: '/profile',
            name: 'profile',
            meta: { middleware: 'auth' },
            redirect: { name: 'profile.show' },
            component: () => import('~/pages/profile/index.vue'),
            children: [
                {
                    path: 'me',
                    name: 'profile.show',
                    component: () => import('~/pages/profile/show.vue'),
                },
            ]
        },
    ]
}
