<template>
    <v-alert :model-value="isInvalid" type="error" icon="mdi-alert" density="compact" closable>
        {{ $t('invalid_email_verification_link') }}
    </v-alert>
</template>

<script setup>
    const { t } = useI18n()
    const route = useRoute()
    const router = useRouter()
    const { $store, $axios, $config } = useNuxtApp()

    const isInvalid = ref(false)

    useHead({
        title: t('verify_email'),
    })

    async function verifyEmail () {
        try {
            const url = new URL(route.query.verify_url)

            $axios.defaults.baseURL = $config.public.appUrl

            await $axios.get(url.href.replace(url.origin, ''))

            router.replace({ name: 'profile.show', query: { verified: 1 } })
        } catch (e) {
            isInvalid.value = e?.response?.status === 403
        }

        $axios.defaults.baseURL = $config.public.apiUrl
        $store.toggleOverlay()
    }

    onMounted (() => {
        $store.toggleOverlay()
        verifyEmail()
    })
</script>
