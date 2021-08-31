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
                        v-if="!item.children"
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
                        v-else
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
            items: [
                {
                    icon: 'mdi-view-dashboard',
                    title: 'Dashboard',
                    route: { name: 'dashboard' },
                },
                {
                    icon: 'mdi-account-group',
                    title: 'Users',
                    route: { name: 'admin.users' }
                },
                {
                    icon: 'mdi-security',
                    title: 'Roles & Permissions',
                    route: { name: 'admin.roles' }
                },
            ],
            links: [
                {
                    text: 'Login',
                    icon: 'mdi-fingerprint',
                    route: { name: 'login' },
                },
                {
                    text: 'Register',
                    icon: 'mdi-account-multiple-plus',
                    route: { name: 'register' },
                },
            ],
        }),

        computed: mapGetters({
            auth: 'auth/check',
            user: 'auth/user',
        }),

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
            }
        },
    }
</script>
