<template>
    <v-row justify="center" align="center">
        <v-col cols="12" sm="8" md="6">
            <div class="mt-12 text-center">
                <logo />
                <vuetify-logo />
            </div>
        </v-col>
    </v-row>
</template>

<script>
    import Logo from '~/components/Logo'
    import VuetifyLogo from '~/components/VuetifyLogo'

    export default {
        components: {
            Logo,
            VuetifyLogo
        },

        head: vm => ({
            title: vm.$t('home'),
        }),
    }
</script>
