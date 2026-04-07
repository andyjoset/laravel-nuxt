<template>
    <v-alert v-if="isValid" v-bind="$attrs" type="info">
        <i18n-t keypath="email_verification_required.text" scope="global">
            <template #action>
                <v-btn variant="text" @click="requestEmailVerificationNotification">
                    {{ $t('email_verification_required.action_text') }}
                </v-btn>
            </template>
        </i18n-t>


        <v-overlay
            contained
            persistent
            :model-value="pending"
            class="align-center justify-center">
            <v-progress-circular indeterminate size="32" color="primary" />
        </v-overlay>
    </v-alert>
</template>

<script setup>
    import { useAuthStore } from '~/store/auth'
    import useHelpers from '~/composables/helpers'

    const { t } = useI18n()
    const { $auth } = useNuxtApp()
    const authStore = useAuthStore()
    const { $notify, dateDiff } = useHelpers()

    const pending = ref(false)
    const lastRequest = ref(null)

    const user = computed(() => authStore.user)

    const isValid = computed(() => {
        if (user.value.email_verified_at === undefined) {
            return false
        }

        const canRequest = lastRequest.value ? dateDiff(new Date(), lastRequest.value, 'hours') > 1 : true

        return !user.value.email_verified_at && canRequest
    })

    async function requestEmailVerificationNotification () {
        lastRequest.value = localStorage.last_email_veritificaion_notification

        if (!isValid.value) {
            return $notify({
                color: 'error',
                message: t(user.value.email_verified_at
                    ? 'email_already_verified'
                    : 'verification_link_recently_sent'
                ),
            })
        }

        pending.value = true

        try {
            await $auth.requestEmailVerificationNotification()

            lastRequest.value = localStorage.last_email_veritificaion_notification = new Date()

            $notify(t('verification_link_sent'))
        } catch (e) {}

        pending.value = false
    }

    onMounted (() => {
        lastRequest.value = localStorage.last_email_veritificaion_notification
    })
</script>
