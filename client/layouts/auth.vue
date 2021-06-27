<template>
    <v-app dark>
        <app-navbar />
        <v-main>
            <v-container>
                <nuxt />
            </v-container>
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

        data: () => ({
        })
    }
</script>
