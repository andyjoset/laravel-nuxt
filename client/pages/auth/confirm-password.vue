<template>
    <confirm-password-form @success="onFormSuccess" />
</template>

<script setup>
    import ConfirmPasswordForm from '~/components/auth/ConfirmPasswordForm'

    const { t } = useI18n()
    const route = useRoute()
    const router = useRouter()
    const { $store } = useNuxtApp()

    useHead({
        title: t('confirm_password'),
    })

    async function onFormSuccess (data) {
        $store.toggleOverlay()

        try {
            await router.replace(route.query.redirect)
        } catch (e) {
        }

        $store.toggleOverlay()
    }

    onMounted (() => {
        if ($store.overlay.show) {
            $store.toggleOverlay()
        }
    })
</script>
