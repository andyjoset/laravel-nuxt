<template>
    <v-app dark>
        <app-navbar />
        <v-main>
            <v-row align="center" justify="center">
                <v-col cols="12" sm="8" md="6">
                    <div class="px-4 pt-4 pb-3">
                        <div class="mt-8 text-h1">
                            Error {{ error.statusCode }}
                        </div>
                        <div class="text-h2">
                            {{ error.message }}
                        </div>
                        <v-btn nuxt text :to="{ name: 'home' }">
                            <!-- eslint-disable-next-line vue/singleline-html-element-content-newline -->
                            <v-icon class="mr-1">mdi-home</v-icon> Home page
                        </v-btn>
                    </div>
                </v-col>
            </v-row>
        </v-main>
        <app-footer />
        <snackbar />

        <v-overlay
            :z-index="10"
            :value="$store.getters['overlay'].show"
            :opacity="$store.getters['overlay'].opacity">
            <v-progress-circular indeterminate size="64" color="primary" />
        </v-overlay>
    </v-app>
</template>

<script>
    import AppFooter from '~/components/AppFooter'
    import AppNavbar from '~/components/AppNavbar'
    import Snackbar from '~/components/Snackbar'

    export default {
        components: {
            AppNavbar,
            AppFooter,
            Snackbar,
        },

        layout: 'empty',

        props: {
            error: {
                type: Object,
                default: null,
            }
        },

        data: () => ({
        }),

        head: vm => ({
            title: vm.error.statusCode === 404 ? '404 Not Found' : 'An error occurred'
        }),
    }
</script>

<style scoped>
    h1 {
        font-size: 20px;
    }
</style>
