<template>
    <v-card
        max-width="95%"
        class="mx-auto mt-6 pa-8">
        <v-card-text>
            <v-row align="center">
                <v-col md="6" sm="12" class="mb-4">
                    <v-list-item three-line>
                        <v-list-item-avatar size="86">
                            <v-avatar size="100%">
                                <img :src="user.photo_url" :alt="user.name">
                            </v-avatar>
                            <v-tooltip v-if="!edit" top>
                                <template #activator="{ on }">
                                    <v-btn
                                        fab
                                        x-small
                                        absolute
                                        size="6"
                                        color="error"
                                        max-width="20"
                                        max-height="20"
                                        class="mt-10 ml-11"
                                        v-on="on"
                                        @click="updateAvatarDialog = true">
                                        <v-icon size="100%">mdi-pencil</v-icon>
                                    </v-btn>
                                </template>
                                <span v-t="{ path: 'change', args: [$t('avatar')] }" />
                            </v-tooltip>
                        </v-list-item-avatar>

                        <v-list-item-content v-if="edit">
                            <update-profile-information-form v-if="edit" @close="edit = false" />
                        </v-list-item-content>

                        <v-list-item-content v-else>
                            <v-list-item-title class="text-h5 my-0">
                                <span v-text="user.name" />
                            </v-list-item-title>
                            <v-list-item-subtitle class="my-0">
                                <v-icon>mdi-email</v-icon> {{ user.email }}
                                <v-btn
                                    color="primary"
                                    x-small
                                    icon
                                    @click="edit = true">
                                    <v-icon>mdi-pencil</v-icon>
                                </v-btn>
                            </v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>
                </v-col>

                <v-col md="6" sm="12" class="mb-4">
                    <v-banner :single-line="!isLg()">
                        <v-avatar slot="icon" color="primary accent-4" size="40">
                            <v-icon icon="mdi-lock" color="white">
                                mdi-lock
                            </v-icon>
                        </v-avatar>

                        <span v-t="'change_password'" />

                        <template #actions>
                            <v-btn
                                icon
                                class="text-left"
                                color="primary"
                                :disabled="edit"
                                @click="updatePasswordDialog = true">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                        </template>
                    </v-banner>
                </v-col>

                <v-col v-if="user.email_verified_at !== undefined" cols="12" class="mb-4">
                    <email-verification-notification-request />
                </v-col>
            </v-row>
        </v-card-text>

        <v-dialog v-model="updatePasswordDialog" persistent max-width="450">
            <update-password-form @close="updatePasswordDialog = false" />
        </v-dialog>

        <v-dialog v-model="updateAvatarDialog" persistent max-width="450">
            <update-avatar-form @close="updateAvatarDialog = false" />
        </v-dialog>
    </v-card>
</template>

<script>
    import UpdateAvatarForm from '~/components/auth/UpdateAvatarForm'
    import UpdatePasswordForm from '~/components/auth/UpdatePasswordForm'
    import UpdateProfileInformationForm from '~/components/auth/UpdateProfileInformationForm'
    import EmailVerificationNotificationRequest from '~/components/auth/EmailVerificationNotificationRequest'

    export default {
        components: {
            UpdateAvatarForm,
            UpdatePasswordForm,
            UpdateProfileInformationForm,
            EmailVerificationNotificationRequest,
        },

        data: vm => ({
            edit: false,
            updateAvatarDialog: false,
            updatePasswordDialog: false,
        }),

        head: vm => ({
            title: vm.$t('profile.me'),
        }),

        computed: {
            user () {
                return this.$store.getters['auth/user']
            }
        },

        created () {
            if (process.browser && parseInt(this.$route.query.verified) === 1) {
                this.$router.replace({ name: this.$route.name })

                this.$nextTick(() => this.$notify({ message: this.$t('email_verified'), timeout: 0 }))
            }
        },
    }
</script>
