<template>
    <v-card
        max-width="95%"
        class="mx-auto mt-6 pa-8">
        <v-card-text>
            <v-row align="center">
                <v-col md="6" sm="12" xs="12" class="mb-4">
                    <v-list-item lines="three">
                        <template #prepend>
                            <v-avatar size="86">
                                <v-img :src="user.photo_url" :alt="user.name" cover />
                            </v-avatar>
                            <v-tooltip v-if="!edit" location="top">
                                <template #activator="{ props }">
                                    <v-btn
                                        color="error"
                                        size="x-small"
                                        max-width="20"
                                        max-height="20"
                                        icon="mdi-pencil"
                                        position="absolute"
                                        class="mt-10 ml-14"
                                        v-bind="props"
                                        @click="updateAvatarDialog = true" />
                                </template>
                                <span v-text="$t('change', [$t('avatar')])" />
                            </v-tooltip>
                        </template>

                        <div v-if="edit" class="mt-n3">
                            <update-profile-information-form
                                v-if="edit"
                                @success="edit = false"
                                @cancel="edit = false" />
                        </div>

                        <div v-else>
                            <v-list-item-title class="text-h5" v-text="user.name" />
                            <v-list-item-subtitle class="my-0">
                                <v-icon icon="mdi-email" /> {{ user.email }}
                                <v-btn
                                    size="small"
                                    variant="text"
                                    color="primary"
                                    icon="mdi-pencil"
                                    class="mx-0 px-0"
                                    @click="edit = true" />
                            </v-list-item-subtitle>
                        </div>
                    </v-list-item>
                </v-col>

                <v-col md="6" sm="12" xs="12" class="mb-4">
                    <v-banner :lines="isLg ? 'one' : 'three'">
                        <v-banner-text>
                            <v-avatar icon="mdi-lock" color="primary accent-4" size="40" class="mr-2" />
                            <span v-text="$t('change_password')" />
                        </v-banner-text>

                        <template #actions>
                            <v-btn
                                color="primary"
                                :disabled="edit"
                                icon="mdi-pencil"
                                @click="updatePasswordDialog = true" />
                        </template>
                    </v-banner>
                </v-col>

                <ClientOnly>
                    <v-col v-if="user.email_verified_at !== undefined" cols="12" class="mb-4">
                        <email-verification-notification-request />
                    </v-col>
                </ClientOnly>
            </v-row>
        </v-card-text>
        <ClientOnly>
            <v-dialog v-model="updatePasswordDialog" persistent max-width="450">
                <update-password-form
                    @cancel="updatePasswordDialog = false"
                    @success="updatePasswordDialog = false" />
            </v-dialog>

            <v-dialog v-model="updateAvatarDialog" persistent max-width="450">
                <update-avatar-form
                    @cancel="updateAvatarDialog = false"
                    @success="updateAvatarDialog = false" />
            </v-dialog>
        </ClientOnly>
    </v-card>
</template>

<script setup>
    import { useAuthStore } from '~/store/auth'
    import useHelpers from '~/composables/helpers'
    import UpdateAvatarForm from '~/components/auth/UpdateAvatarForm'
    import UpdatePasswordForm from '~/components/auth/UpdatePasswordForm'
    import UpdateProfileInformationForm from '~/components/auth/UpdateProfileInformationForm'
    import EmailVerificationNotificationRequest from '~/components/auth/EmailVerificationNotificationRequest'

    const { t } = useI18n()
    const route = useRoute()
    const router = useRouter()
    const authStore = useAuthStore()
    const { $notify, isLg } = useHelpers()

    useHead({
        title: t('profile.me'),
    })

    const edit = ref(false)
    const updateAvatarDialog = ref(false)
    const updatePasswordDialog = ref(false)

    const user = computed(() => authStore.user)

    onMounted(() => {
        if (parseInt(route.query.verified) === 1) {
            router.replace({ name: route.name })
            nextTick(() => $notify({ message: t('email_verified'), timeout: -1 }))
        }
    })
</script>
