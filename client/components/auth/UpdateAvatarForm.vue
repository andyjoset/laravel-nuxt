<template>
    <app-form
        :form="form"
        :disabled="form.busy"
        action="/user/avatar"
        icon="mdi-account-circle"
        :title="$t('update', [$t('avatar')])"
        :container-options="{ class: 'px-2' }"
        v-on="$attrs"
        @cancel="clearForm"
        @success="onFormSuccess">
        <v-list-item three-line class="mb-2">
            <template #prepend>
                <v-avatar :image="preview" size="100">
                    <v-img :src="preview" />
                </v-avatar>
            </template>
            <v-file-input
                v-model="selectedAvatar"
                clearable
                show-size
                accept="image/*,"
                variant="underlined"
                :label="$t('labels.avatar')"
                :error="form.errors.has('avatar')"
                :error-messages="form.errors.get('avatar')" />
        </v-list-item>

        <template #actions="{ handleCancelClick, submit }">
            <v-card-actions>
                <v-spacer />
                <v-tooltip location="top">
                    <template #activator="{ props }">
                        <v-btn
                            icon
                            size="small"
                            color="error"
                            variant="elevated"
                            :disabled="form.busy"
                            v-bind="props"
                            @click="handleCancelClick">
                            <v-icon size="26" icon="mdi-close-circle" />
                        </v-btn>
                    </template>
                    <span v-text="$t('btns.cancel')" />
                </v-tooltip>

                <v-tooltip v-if="showDeleteBtn" location="top">
                    <template #activator="{ props }">
                        <v-btn
                            icon
                            size="small"
                            color="info"
                            variant="elevated"
                            :loading="form.busy"
                            v-bind="props"
                            @click="restoreAvatar(submit)">
                            <v-icon size="26" icon="mdi-restore" />
                        </v-btn>
                    </template>
                    <span v-text="$t('delete', [$t('avatar')])" />
                </v-tooltip>

                <v-tooltip v-if="showUploadBtn" location="top">
                    <template #activator="{ props }">
                        <v-btn
                            icon
                            size="small"
                            color="primary"
                            variant="elevated"
                            :loading="form.busy"
                            v-bind="props"
                            @click="updateAvatar(submit)">
                            <v-icon size="26" icon="mdi-upload" />
                        </v-btn>
                    </template>
                    <span v-text="$t('upload', [$t('avatar')])" />
                </v-tooltip>
            </v-card-actions>
        </template>
    </app-form>
</template>

<script setup>
    import useForm from '~/composables/form'
    import useHelpers from '~/composables/helpers'
    import { useAuthStore } from '~/store/auth'

    const authStore = useAuthStore()
    const { t } = useI18n()
    const { $notify, blank } = useHelpers()
    const { form, clearForm, formHasFiles } = useForm({
        avatar: null,
        _method: 'PUT',
    })

    formHasFiles.value = true

    const selectedAvatar = ref(null)
    const user = computed (() => authStore.user)
    const preview = computed (() => {
        if (blank(selectedAvatar.value)) {
            return user.value.photo_url
        }

        return URL.createObjectURL(selectedAvatar.value)
    })

    const isUpdating = computed (() => form._method && form._method === 'PUT')
    const showUploadBtn = computed (() => !form.busy || !form._method || isUpdating.value)
    const showDeleteBtn = computed (() =>
        !user.value.photo_url.includes('default-avatar.png') &&
        (!form.busy || !form._method || form._method === 'DELETE'))

    function restoreAvatar (submit) {
        form._method = 'DELETE'
        submit()
    }

    function updateAvatar (submit) {
        form._method = 'PUT'
        form.avatar = selectedAvatar.value
        submit()
    }

    function onFormSuccess (data) {
        authStore.updateUser(data)
        $notify(t('alerts.' + (isUpdating.value ? 'updated' : 'deleted')))
        clearForm()
    }
</script>
