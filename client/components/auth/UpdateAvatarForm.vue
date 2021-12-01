<template>
    <app-form
        :form="form"
        :disabled="form.busy"
        action="/user/avatar"
        icon="mdi-account-circle"
        :title="$t('update', [$t('avatar')])"
        :container-options="{ class: 'px-2' }"
        v-on="$listeners"
        @cancel="clearForm"
        @success="onFormSuccess">
        <v-list-item three-line class="mb-2">
            <v-list-item-avatar size="100">
                <v-avatar size="100%">
                    <img :src="preview" :alt="user.name">
                </v-avatar>
            </v-list-item-avatar>
            <v-list-item-content class="mt-6">
                <v-file-input
                    v-model="form.avatar"
                    clearable
                    show-size
                    accept="image/*,"
                    :label="$t('labels.avatar')"
                    :error="form.errors.has('avatar')"
                    :error-messages="form.errors.get('avatar')" />
            </v-list-item-content>
        </v-list-item>

        <template #actions="{ handleCancelClick, submit }">
            <v-card-actions>
                <v-spacer />
                <v-tooltip top>
                    <template #activator="{ on }">
                        <v-btn
                            small
                            color="error"
                            class="mr-2 v-btn--round v-btn--fab"
                            :disabled="form.busy"
                            @click="handleCancelClick"
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
                            @click="restoreAvatar(submit)"
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
                            @click="updateAvatar(submit)"
                            v-on="on">
                            <v-icon size="26">mdi-upload</v-icon>
                        </v-btn>
                    </template>
                    <span v-t="{ path: 'upload', args: [$t('avatar')] }" />
                </v-tooltip>
            </v-card-actions>
        </template>
    </app-form>
</template>

<script>
    import HasForm from '~/components/mixins/HasForm'

    export default {
        mixins: [HasForm],

        data: vm => ({
            formHasFiles: true,
            form: vm.$vform.make({
                avatar: null,
                _method: 'PUT',
            }),
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
                return !this.form.busy || !this.form._method || this.isUpdating
            },
            showDeleteBtn () {
                return !this.user.photo_url.includes('default-avatar.png') &&
                    (!this.form.busy || !this.form._method || this.form._method === 'DELETE')
            },
            isUpdating () {
                return this.form._method && this.form._method === 'PUT'
            },
        },

        methods: {
            restoreAvatar (submit) {
                this.form._method = 'DELETE'

                submit()
            },
            updateAvatar (submit) {
                this.form._method = 'PUT'

                submit()
            },
            onFormSuccess (data) {
                this.$store.commit('auth/UPDATE_USER', data)

                this.$notify(
                    this.$t('alerts.' + (this.isUpdating ? 'updated' : 'deleted'))
                )

                this.clearForm()
            },
        }
    }
</script>
