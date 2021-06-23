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

        data: () => ({
        })
    }
</script>
