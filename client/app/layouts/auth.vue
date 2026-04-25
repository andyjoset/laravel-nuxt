<template>
    <v-app>
        <app-navbar />
        <v-main>
            <v-container>
                <slot />
            </v-container>
        </v-main>
        <app-footer />
        <snackbar />
        <overlay-loader />
    </v-app>
</template>

<script setup>
    import AppFooter from '~/components/AppFooter'
    import AppNavbar from '~/components/AppNavbar'
    import Snackbar from '~/components/Snackbar'
    import OverlayLoader from '~/components/OverlayLoader'
</script>
