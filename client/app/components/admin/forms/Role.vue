<template>
    <app-form
        :form="form"
        :title="title"
        :action="action"
        :method="method"
        :readonly="readonly"
        :disabled="form.busy || store.fetchingPermissions"
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

        <v-row v-if="showPermissions">
            <v-col cols="12" class="pb-0 mb-0">
                <strong :class="form.errors.has('permissions') ? 'red--text' : ''">
                    {{ $t('permissions', 2) }}:
                </strong>
                <p class="red--text" v-text="form.errors.get('permissions')" />
            </v-col>

            <v-col v-if="store.fetchingPermissions" cols="4" offset="4" class="text-center">
                <v-progress-circular indeterminate size="26" width="3" color="primary" />
            </v-col>

            <v-col v-for="(option, i) in Object.keys(permissions)" :key="i" sm="12" md="6" lg="6" xl="3">
                <v-treeview
                    open-all
                    item-value="id"
                    density="compact"
                    select-strategy="classic"
                    selected-color="primary"
                    :selectable="!$attrs.readonly"
                    :items="[permissions[option]]"
                    :model-value="$attrs.readonly ? [] : getTreeSelection(option)"
                    @update:selected="updateSelection($event, option)" />
            </v-col>
        </v-row>
    </app-form>
</template>

<script setup>
    import useForm from '~/composables/form'
    import useHelpers from '~/composables/helpers'
    import { useCommonStore } from '~/store/commons'

    const props = defineProps({
        role: {
            type: Object,
            required: false,
            default: () => null,
        },
    })

    const attrs = useAttrs()
    const { t } = useI18n()
    const store = useCommonStore()
    const { groupByKey, pluck } = useHelpers()
    const { form, method, onFormSuccess, onFormCancelled } = useForm({
        name: '',
        permissions: [],
    }, props.role, getFormValues)

    const title = computed(() => {
        if (attrs.readonly) {
            return t('role_info')
        }

        return t(props.role ? 'edit' : 'create', [t('roles')])
    })

    const action = computed(() => props.role ? `/admin/roles/${props.role.id}` : '/admin/roles')
    const showPermissions = computed(() => props.role?.name !== 'Super Admin')

    const permissions = computed(() => {
        const items = {}
        const permissions = store.permissions

        for (const option in groupByKey(permissions, 'module')) {
            const children = permissions.filter((permission) => {
                const result = permission.module === option

                if (attrs.readonly) {
                    return result && pluck(props.role.permissions, 'id').includes(permission.id)
                }

                return result
            })

            if (attrs.readonly && !children.length) {
                children.push({ id: 'N/A', description: 'N/A' })
            }

            items[option] = {
                id: permissions.indexOf(option),
                title: option,
                children: children.map(p => Object({ id: p.id, title: p.description }))
            }
        }

        return items
    })

    function getFormValues () {
        const values = { ...props.role, permissions: [] }

        if (props.role) {
            values.permissions = pluck(props.role.permissions, 'id')
        }

        return values
    }

    function updateSelection (values, scope) {
        const ids = pluck(permissions.value[scope].children, 'id')
        const result = form.permissions.filter(id => !ids.includes(id))

        result.push(...values)
        form.permissions = result
    }

    function getTreeSelection (scope) {
        const ids = pluck(permissions.value[scope].children, 'id')
        const result = form.permissions.filter(id => ids.includes(id))

        return result
    }

    onMounted (() => {
        if (!store.permissions.length) {
            store.fetchPermissions()
        }
    })
</script>
