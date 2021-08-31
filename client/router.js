import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

const page = path => () => import(`~/pages/${path}`).then(m => m.default || m)

export function createRouter () {
    return new Router({
        mode: 'history',
        routes: [
            {
                path: '/a',
                name: 'auth',
                redirect: { name: 'login' },
                component: page('auth/index'),
                children: [
                    {
                        path: 'login',
                        name: 'login',
                        component: page('auth/login')
                    },
                    {
                        path: 'register',
                        name: 'register',
                        component: page('auth/register')
                    },
                    {
                        path: 'reset-password/:token',
                        name: 'password.reset',
                        component: page('auth/reset-password')
                    },
                ]
            },

            {
                name: 'home',
                path: '/',
                component: page('index'),
            },
            {
                name: 'about',
                path: '/about',
                component: page('about'),
            },
            {
                name: 'contact',
                path: '/contact',
                component: page('contact'),
            },
            {
                name: 'dashboard',
                path: '/dashboard',
                component: page('dashboard'),
            },

            // Admin routes
            {
                path: '/admin',
                name: 'admin.index',
                component: page('admin/index'),
                children: [
                    {
                        path: 'users',
                        name: 'admin.users',
                        component: page('admin/users'),
                    },
                    {
                        path: 'roles',
                        name: 'admin.roles',
                        component: page('admin/roles'),
                    },
                ]
            },

            // Profile routes
            {
                path: '/profile',
                name: 'profile',
                redirect: { name: 'profile.show' },
                component: page('profile/index'),
                children: [
                    {
                        path: 'me',
                        name: 'profile.show',
                        component: page('profile/show'),
                    },
                ]
            },
        ]
    })
}
