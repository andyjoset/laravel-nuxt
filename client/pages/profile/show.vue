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
                        Change current password
                        <template #actions>
                            <v-btn
                                text
                                class="text-left"
                                color="primary"
                                :disabled="edit"
                                @click="updatePasswordDialog = true">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                        </template>
                    </v-banner>
                </v-col>
            </v-row>
        </v-card-text>

        <v-dialog v-model="updatePasswordDialog" persistent max-width="450">
            <update-password-form @close="updatePasswordDialog = false" />
        </v-dialog>
    </v-card>
</template>

<script>
    import UpdatePasswordForm from '~/components/auth/UpdatePasswordForm'
    import UpdateProfileInformationForm from '~/components/auth/UpdateProfileInformationForm'

    export default {
        components: {
            UpdatePasswordForm,
            UpdateProfileInformationForm,
        },

        data: vm => ({
            edit: false,
            updatePasswordDialog: false,
        }),

        head: () => ({
            title: 'My Profile'
        }),

        computed: {
            user () {
                return this.$store.getters['auth/user']
            }
        },
    }
</script>
