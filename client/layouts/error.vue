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
                            <v-icon class="mr-1">mdi-home</v-icon> Home page
                        </v-btn>
                    </div>
                </v-col>
            </v-row>
        </v-main>
        <app-footer />
        <snackbar />

        <overlay-loader />
    </v-app>
</template>

<script>
    import AppFooter from '~/components/AppFooter'
    import AppNavbar from '~/components/AppNavbar'
    import Snackbar from '~/components/Snackbar'
    import OverlayLoader from '~/components/OverlayLoader'

    export default {
        components: {
            AppNavbar,
            AppFooter,
            Snackbar,
            OverlayLoader,
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
