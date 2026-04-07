<template>
    <v-card tile class="mx-auto overflow-hidden">
        <v-navigation-drawer
            v-if="auth"
            v-model="drawer"
            order="1"
            :rail="navState.mini">
            <v-list density="compact" nav>
                <template v-for="item in items">
                    <v-list-item
                        v-if="!item.children && (item.permission ? $canAny(item.permission) : true)"
                        :key="item.title"
                        :to="item.route"
                        :title="item.title"
                        :prepend-icon="item.icon" />

                    <v-list-group
                        v-else-if="showGroupedItems(item)"
                        :key="item.title"
                        :prepend-icon="item.icon"
                        :value="true">
                        <template #activator="{ props }">
                            <v-list-item v-bind="props" :title="item.title" />
                        </template>

                        <template v-for="child in item.children">
                            <v-list-item
                                v-if="child.permission ? $canAny(child.permission) : true"
                                :key="child.title"
                                :to="child.route"
                                :title="child.title"
                                :append-icon="child.icon" />
                        </template>
                    </v-list-group>
                </template>
            </v-list>
        </v-navigation-drawer>

        <v-app-bar fixed>
            <template v-if="auth">
                <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
                <v-btn
                    v-if="drawer && !isXs"
                    :icon="`mdi-chevron-${navState.mini ? 'right' : 'left'}`"
                    @click.stop="navState.mini = !navState.mini" />
            </template>

            <v-app-bar-title class="pl-0">
                <v-btn
                    variant="plain"
                    :to="{ name: 'home' }"
                    class="pa-0 justify-start">
                    <span class="d-flex d-sm-none" v-text="acronym($config.public.appName)" />
                    <span class="d-none d-sm-flex" v-text="$config.public.appName" />
                </v-btn>
            </v-app-bar-title>
            <v-spacer />

            <template v-if="!auth">
                <v-btn
                    v-for="link in links"
                    :key="`app_nav_${link.text}_link`"
                    :to="link.route"
                    variant="plain">
                    <template #prepend>
                        <v-icon size="small" :icon="link.icon" />
                    </template>
                    <span v-text="link.text" />
                </v-btn>
            </template>
            <nav-dropdown v-else />
        </v-app-bar>
    </v-card>
</template>

<script setup>
    import { useAuthStore } from '~/store/auth'
    import useHelpers from '~/composables/helpers'
    import NavDropdown from '~/components/auth/NavDropdown'

    const { t } = useI18n()
    const authStore = useAuthStore()
    const { isXs, acronym, pluck, $canAny } = useHelpers()
    const navState = useCookie('drawer', {
        default: () => ({ active: true, mini: true }),
    })

    const drawer = ref(navState.value.active)

    const items = computed(() => [
        {
            icon: 'mdi-view-dashboard',
            title: t('dashboard.title'),
            route: { name: 'dashboard' },
        },
        {
            icon: 'mdi-account-group',
            title: t('users', 2),
            route: { name: 'admin.users' },
            permission: ['users.index'],
        },
        {
            icon: 'mdi-security',
            title: t('roles_and_permissions'),
            route: { name: 'admin.roles' },
            permission: ['roles.index'],
        },
    ])

    const links = computed(() => [
        {
            text: t('login'),
            icon: 'mdi-fingerprint',
            route: { name: 'login' },
        },
        {
            text: t('register'),
            icon: 'mdi-account-multiple-plus',
            route: { name: 'register' },
        },
    ])

    const auth = computed(() => authStore.check)

    function showGroupedItems (item) {
        const permissions = pluck(item.children, 'permission').flat()
        return item.children && (permissions.length ? $canAny(permissions) : true)
    }

    watch(drawer, (value) => {
        navState.value.active = value
    })

    onMounted(() => {
        nextTick(() => {
            drawer.value = navState.value.active
        })
    })
</script>
