<template>
    <v-row justify="center" align="center">
        <v-col cols="12" sm="8" md="6">
            <ClientOnly>
                <div class="mt-12 text-center">
                    <logo />
                    <vuetify-logo />
                </div>
            </ClientOnly>
        </v-col>
    </v-row>
</template>

<script setup>
    import Logo from '~/components/Logo'
    import VuetifyLogo from '~/components/VuetifyLogo'

    const { t } = useI18n()

    useHead({
        title: t('home'),
    })
</script>
