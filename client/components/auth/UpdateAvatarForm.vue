<template>
    <v-card width="100%" height="100%">
        <v-card-text>
            <v-card-title class="my-0">
                <v-sheet
                    rounded
                    width="100%"
                    elevation="6"
                    max-width="100%"
                    color="primary"
                    class="text-center">
                    <v-theme-provider>
                        <div class="pa-7">
                            <span class="text-h5">
                                <v-icon>
                                    mdi-account-circle
                                </v-icon>
                                {{ $t('update', [$t('avatar')]) }}
                            </span>
                        </div>
                    </v-theme-provider>
                </v-sheet>
            </v-card-title>

            <v-list-item three-line class="mb-2">
                <v-list-item-avatar size="100">
                    <v-avatar size="100%">
                        <img :src="preview" :alt="user.name">
                    </v-avatar>
                </v-list-item-avatar>
                <v-list-item-content class="mt-6">
                    <v-form :disabled="form.busy" @submit.prevent="submit">
                        <v-file-input
                            v-model="form.avatar"
                            clearable
                            show-size
                            accept="image/*,"
                            :label="$t('labels.avatar')"
                            :error="form.errors.has('avatar')"
                            :error-messages="form.errors.get('avatar')" />
                    </v-form>
                </v-list-item-content>
            </v-list-item>
        </v-card-text>

        <v-divider class="mx-4" />

        <v-card-actions>
            <v-spacer />
            <v-tooltip top>
                <template #activator="{ on }">
                    <v-btn
                        small
                        color="error"
                        class="mr-2 v-btn--round v-btn--fab"
                        :disabled="form.busy"
                        @click="close"
                        v-on="on">
                        <v-icon size="26">mdi-close-circle</v-icon>
                    </v-btn>
                </template>
                <span v-t="'btns.cancel'" />
            </v-tooltip>

            <v-tooltip v-if="showDeleteBtn" top>
                <template #activator="{ on }">
                    <v-btn
                        small
                        color="info"
                        class="mr-2 v-btn--round v-btn--fab"
                        :loading="form.busy"
                        @click="submit('delete')"
                        v-on="on">
                        <v-icon size="26">mdi-restore</v-icon>
                    </v-btn>
                </template>
                <span v-t="{ path: 'delete', args: [$t('avatar')] }" />
            </v-tooltip>

            <v-tooltip v-if="showUploadBtn" top>
                <template #activator="{ on }">
                    <v-btn
                        small
                        color="primary"
                        class="v-btn--round v-btn--fab"
                        :loading="form.busy"
                        @click="submit('update')"
                        v-on="on">
                        <v-icon size="26">mdi-upload</v-icon>
                    </v-btn>
                </template>
                <span v-t="{ path: 'upload', args: [$t('avatar')] }" />
            </v-tooltip>
        </v-card-actions>
    </v-card>
</template>

<script>
    export default {
        data: vm => ({
            form: vm.$vform.make({
                avatar: null,
            }),
            deleting: false
        }),

        computed: {
            user () {
                return this.$store.getters['auth/user']
            },
            preview () {
                if (this.form.avatar === null) {
                    return this.user.photo_url
                }

                return URL.createObjectURL(this.form.avatar)
            },
            showUploadBtn () {
                return !this.form.busy || !this.form._method || this.form._method === 'PUT'
            },
            showDeleteBtn () {
                return !this.user.photo_url.includes('default-avatar.png') &&
                    (!this.form.busy || !this.form._method || this.form._method === 'DELETE')
            },
        },

        methods: {
            async submit (action) {
                const isUpdate = action === 'update'
                this.form._method = isUpdate ? 'PUT' : 'DELETE'

                try {
                    const { data } = await this.form.post('/user/avatar')

                    this.$store.commit('auth/UPDATE_USER', data)

                    this.$notify(
                        this.$t('alerts.' + (isUpdate ? 'updated' : 'deleted'))
                    )

                    this.close()
                } catch (e) {
                }
            },
            close () {
                this.$emit('close')
                this.form.clear()
                this.form.reset()
            }
        }
    }
</script>
