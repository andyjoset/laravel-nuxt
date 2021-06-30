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
            <v-list>
                <v-list-item
                    v-for="(item, i) in items"
                    :key="i"
                    :to="item.to"
                    router
                    exact>
                    <v-list-item-action>
                        <v-icon>{{ item.icon }}</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title v-text="item.title" />
                    </v-list-item-content>
                </v-list-item>
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
                <v-btn text nuxt :plain="routeIs('login')" :to="{ name: 'login' }" class="mx-0">
                    <v-icon small class="mr-2">
                        mdi-fingerprint
                    </v-icon>
                    Login
                </v-btn>
                <v-btn text nuxt :plain="routeIs('register')" :to="{ name: 'register' }" class="mx-0">
                    <v-icon small class="mr-2">
                        mdi-account-multiple-plus
                    </v-icon>
                    Register
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
                    to: { name: 'dashboard' },
                }
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
