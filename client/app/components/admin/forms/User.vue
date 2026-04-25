<template>
    <app-form
        :form="form"
        :title="title"
        :action="action"
        :method="method"
        :readonly="readonly"
        :disabled="form.busy"
        @success="onFormSuccess"
        @cancel="onFormCancelled">
        <v-text-field
            v-model="form.name"
            autofocus
            class="my-4"
            density="compact"
            variant="underlined"
            prepend-icon="mdi-face"
            :label="$t('labels.name')"
            :error="form.errors.has('name')"
            :error-messages="form.errors.get('name')" />

        <v-text-field
            v-model="form.email"
            class="mb-4"
            type="email"
            density="compact"
            variant="underlined"
            prepend-icon="mdi-email"
            :label="$t('labels.email')"
            :error="form.errors.has('email')"
            :error-messages="form.errors.get('email')" />

        <app-select
            v-if="$can('users.assign-role')"
            v-model="form.role_id"
            item-value="id"
            item-title="name"
            density="compact"
            variant="underlined"
            server-action="/roles"
            prepend-icon="mdi-shield"
            :label="$t('labels.role')"
            :error="form.errors.has('role_id')"
            :error-messages="form.errors.get('role_id')" />
    </app-form>
</template>

<script setup>
    import useForm from '~/composables/form'
    import useHelpers from '~/composables/helpers'

    const props = defineProps({
        user: {
            type: Object,
            required: false,
            default: () => null,
        },
    })

    const { t } = useI18n()
    const attrs = useAttrs()
    const { $can } = useHelpers()

    const { form, method, onFormSuccess, onFormCancelled } = useForm({
        name: '',
        email: '',
        role_id: null,
    }, props.user, getformvalues)

    const title = computed(() => {
        if (attrs.readonly) {
            return t('user_info')
        }

        return t(props.user ? 'edit' : 'create', [t('users')])
    })

    const action = computed(() => props.user ? `/admin/users/${props.user.id}` : '/admin/users')

    function getformvalues () {
        const values = { ...props.user }

        if (props.user) {
            values.role_id = props.user.roles[0]?.id || null
        }

        return values
    }
</script>
