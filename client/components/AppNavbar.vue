<template>
    <v-card tile class="mx-auto overflow-hidden">
        <v-navigation-drawer
            v-if="auth"
            v-model="drawer"
            :mini-variant="miniVariant"
            height="100%"
            clipped
            fixed
            app
            @transitionend="updateLocalStorate">
            <v-list dense nav>
                <template v-for="item in items">
                    <v-list-item
                        v-if="!item.children && (item.permission ? $canAny(item.permission) : true)"
                        :key="item.title"
                        :to="item.route"
                        nuxt>
                        <v-list-item-icon>
                            <v-icon v-text="item.icon" />
                        </v-list-item-icon>

                        <v-list-item-content>
                            <v-list-item-title v-text="item.title" />
                        </v-list-item-content>
                    </v-list-item>

                    <v-list-group
                        v-else-if="showGroupedItems(item)"
                        :key="item.title"
                        :prepend-icon="item.icon"
                        :value="true"
                        no-action>
                        <template #activator>
                            <v-list-item-content>
                                <v-list-item-title v-text="item.title" />
                            </v-list-item-content>
                        </template>

                        <template v-for="child in item.children">
                            <v-list-item
                                v-if="child.permission ? $canAny(child.permission) : true"
                                :key="child.title"
                                :to="child.route"
                                nuxt>
                                <v-list-item-title v-text="child.title" />

                                <v-list-item-icon>
                                    <v-icon v-text="child.icon" />
                                </v-list-item-icon>
                            </v-list-item>
                        </template>
                    </v-list-group>
                </template>
            </v-list>
        </v-navigation-drawer>

        <v-app-bar
            clipped-left
            fixed
            app>
            <template v-if="auth">
                <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
                <v-btn
                    v-if="drawer"
                    icon
                    @click.stop="miniVariant = !miniVariant">
                    <v-icon>mdi-{{ `chevron-${miniVariant ? 'right' : 'left'}` }}</v-icon>
                </v-btn>
            </template>

            <v-app-bar-title>
                <v-btn plain :to="{ name: 'home' }" v-text="isXs() ? acronym($config.appName) : $config.appName" />
            </v-app-bar-title>
            <v-spacer />

            <template v-if="!auth">
                <v-btn
                    v-for="link in links"
                    :key="link.text"
                    :to="link.route"
                    :plain="routeIs(link.route)"
                    class="mx-0"
                    text
                    nuxt>
                    <v-icon small class="mr-2">{{ link.icon }}</v-icon> {{ link.text }}
                </v-btn>
            </template>

            <nav-dropdown v-else />
        </v-app-bar>
    </v-card>
</template>

<script>
    import { mapGetters } from 'vuex'
    import NavDropdown from '~/components/auth/NavDropdown'

    export default {
        components: {
            NavDropdown,
        },

        data: () => ({
            drawer: true,
            miniVariant: false,
        }),

        computed: {
            items () {
                return [
                    {
                        icon: 'mdi-view-dashboard',
                        title: this.$t('dashboard.title'),
                        route: { name: 'dashboard' },
                    },
                    {
                        icon: 'mdi-account-group',
                        title: this.$tc('users', 2),
                        route: { name: 'admin.users' },
                        permission: ['users.index'],
                    },
                    {
                        icon: 'mdi-security',
                        title: this.$t('roles_and_permissions'),
                        route: { name: 'admin.roles' },
                        permission: ['roles.index'],
                    },
                ]
            },
            links () {
                return [
                    {
                        text: this.$t('login'),
                        icon: 'mdi-fingerprint',
                        route: { name: 'login' },
                    },
                    {
                        text: this.$t('register'),
                        icon: 'mdi-account-multiple-plus',
                        route: { name: 'register' },
                    },
                ]
            },
            ...mapGetters({
                auth: 'auth/check',
                user: 'auth/user',
            }),
        },

        created () {
            if (process.browser) {
                const navState = this.getNavStateFromLocalStorate()

                this.drawer = Boolean(navState.drawer)
                this.miniVariant = Boolean(navState.miniVariant)
            }
        },

        methods: {
            updateLocalStorate () {
                const navState = this.getNavStateFromLocalStorate()

                Object.assign(navState, {
                    drawer: this.drawer,
                    miniVariant: this.miniVariant,
                })

                localStorage.app_nav = JSON.stringify(navState)
            },
            getNavStateFromLocalStorate () {
                return JSON.parse(localStorage.app_nav || '{}')
            },
            showGroupedItems (item) {
                const permissions = this.pluck(item.children, 'permission').flat()

                return item.children && (permissions.length ? this.$canAny(permissions) : true)
            },
        },
    }
</script>
