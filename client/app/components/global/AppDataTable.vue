<template>
    <app-table
        :loader-height="2"
        :headers="headerItems"
        :search="currentSearch"
        :items-per-page="itemsPerPage"
        v-bind="$attrs"
        class="elevation-2 px-2">
        <!-- eslint-disable vue/no-unused-vars -->
        <template v-for="(_, slotName) in $slots" #[slotName]="slotProps">
            <slot :name="slotName" v-bind="slotProps" />
        </template>
        <!-- eslint-enable vue/no-unused-vars -->

        <template #append-top>
            <v-toolbar flat class="px-2">
                <v-tooltip v-if="actionCanBeRendered(null, 'store')" location="top">
                    <template #activator="{ props: tooltip }">
                        <v-btn
                            v-bind="tooltip"
                            size="small"
                            elevation="2"
                            color="primary"
                            variant="elevated"
                            :disabled="$attrs.loading"
                            @click="dialogForm = true">
                            <v-icon size="small" :icon="actionCreate.icon || 'mdi-plus-box-multiple'" />
                        </v-btn>
                    </template>
                    <span v-text="actionCreate.text || $t('add', [$t('item')])" />
                </v-tooltip>

                <slot v-if="filters.enabled || $slots.filters" name="filters">
                    <v-menu
                        v-model="filtersMenu"
                        top
                        right
                        offset-x
                        origin="bottom left"
                        transition="scale-transition"
                        :close-on-content-click="false"
                        v-bind="filters.menu">
                        <template #activator="{ props: menu }">
                            <v-tooltip location="top">
                                <template #activator="{ props: tooltip }">
                                    <v-btn
                                        size="small"
                                        color="blue"
                                        variant="elevated"
                                        elevation="2"
                                        class="ml-2"
                                        :disabled="$attrs.loading"
                                        v-bind="{ ...tooltip, ...menu }">
                                        <v-icon size="small" :icon="filters.icon || 'mdi-filter'" />
                                    </v-btn>
                                </template>
                                <span v-text="filters.text || $t('show', [$t('filters')])" />
                            </v-tooltip>
                        </template>

                        <slot name="filters.options" v-bind="filtersOptionsSlotBindings">
                            <v-card>
                                <v-list>
                                    <v-list-item>
                                        {{ $t('filter_by') }}
                                        <v-tooltip location="top">
                                            <template #activator="{ props: tooltip }">
                                                <v-btn
                                                    fixed
                                                    right
                                                    size="small"
                                                    icon="mdi-close"
                                                    v-bind="tooltip"
                                                    @click="filtersMenu = false" />
                                            </template>
                                            <span v-text="$t('btns.close')" />
                                        </v-tooltip>
                                    </v-list-item>

                                    <v-divider class="mx-2" />

                                    <slot name="filters.items" v-bind="filtersOptionsSlotBindings" />
                                </v-list>
                            </v-card>
                        </slot>
                    </v-menu>
                </slot>

                <slot name="append-top-actions" />

                <v-divider v-if="hasTopActions" class="mx-4" inset vertical />
                <v-spacer />

                <v-text-field
                    v-if="!hideInputSearch"
                    v-model="currentSearch"
                    clearable
                    single-line
                    variant="underlined"
                    :label="$t('search')"
                    :hide-details="!currentSearch"
                    :hint="$t('search_hint')"
                    append-icon="mdi-magnify"
                    :disabled="$attrs.loading"
                    :persistent-hint="Boolean(currentSearch)"
                    @keydown.enter="onInputSearchEnter"
                    @click:clear="onInputSearchCleared" />

                <ClientOnly>
                    <v-dialog
                        v-if="dialogFormComponentPath || $slots.form"
                        v-model="dialogForm"
                        persistent
                        v-bind="{ 'max-width': '500px', ...dialogFormProps }">
                        <slot name="form">
                            <component
                                :is="dialogFormComponent"
                                v-bind="{ ...formProps, [itemName]: selectedItem, readonly }"
                                @success="closeDialog"
                                @cancel="closeDialog(null)"
                                @close="closeDialog(null)" />
                        </slot>
                    </v-dialog>
                </ClientOnly>
            </v-toolbar>
        </template>

        <template #[`item.actions`]="slotProps">
            <client-only>
                <template v-for="action in itemActions">
                    <v-tooltip
                        v-if="actionCanBeRendered(slotProps.item, action.name)"
                        :key="`${action.name}_${slotProps.item[itemId]}`"
                        location="top">
                        <template #activator="{ props: tooltip }">
                            <v-icon
                                v-bind="tooltip"
                                small
                                :color="action.color"
                                :disabled="action.disabled || $attrs.loading"
                                :icon="action.icon"
                                @click="action.onClick(slotProps.item)"/>
                        </template>
                        <span v-text="action.text" />
                    </v-tooltip>
                </template>
            </client-only>

            <slot name="append-actions" v-bind="slotProps" />
        </template>

        <template v-if="!$slots.bottom" #bottom>
            <div>
                <v-divider />
                <app-pagination
                    ref="pagination"
                    v-model="currentPage"
                    :config="pagination"
                    :use-router="useRouterForPagination"
                    :disabled="$attrs.loading"
                    :append-query="appendQuery"
                    v-bind="pagination.props"
                    @input="onPaginate" />
            </div>
        </template>
    </app-table>
</template>

<script setup>
    import useHelpers from '~/composables/helpers'

    const forms = import.meta.glob('../admin/forms/*.vue')
    const props = defineProps({
        name: {
            type: String,
            required: true,
        },
        headers: {
            type: Object,
            required: true,
        },
        itemName: {
            type: String,
            default: null,
        },
        serverAction: {
            type: String,
            required: true,
        },
        serverParams: {
            type: Object,
            default: () => ({}),
        },
        pagination: {
            type: Object,
            required: true,
        },
        paginatorProps: {
            type: Object,
            default: () => ({}),
        },
        paginationStrategy: {
            type: String,
            default: 'router',
            validator: value => ['router', 'fetch'].includes(value)
        },
        actions: {
            type: [Array, Object],
            default: () => ['store', 'show', 'update', 'delete'],
            validator: (actions) => {
                const values = Array.isArray(actions) ? actions : Object.keys(actions)
                for (const action of values) {
                    const name = typeof action === 'object' ? action.name : action
                    if (!['store', 'show', 'update', 'delete'].includes(name)) {
                        return false
                    }
                }

                return values.length <= 4
            },
        },
        actionCreate: {
            type: Object,
            default: () => ({}),
        },
        actionsValidations: {
            type: Object,
            default: () => ({}),
            validator: (actions) => {
                for (const option in actions) {
                    const isValid = typeof actions[option].callback === 'function' ||
                        actions[option].skipPermissionCheck ||
                        actions[option].permission

                    if (!isValid) {
                        return false
                    }
                }

                return true
            },
        },
        filters: {
            type: Object,
            default: () => ({}),
        },
        dialogFormComponentPath: {
            type: String,
            default: () => undefined,
        },
        dialogFormProps: {
            type: Object,
            default: () => ({}),
        },
        formProps: {
            type: Object,
            default: () => ({}),
        },
        searchKey: {
            type: String,
            default: 's',
        },
        hideInputSearch: {
            type: Boolean,
            default: () => false,
        },
    })

    const emit = defineEmits(['item-created', 'item-updated', 'item-deleted', 'paginating'])

    const { t } = useI18n()
    const attrs = useAttrs()
    const slots = useSlots()
    const route = useRoute()
    const { $axios, $swal } = useNuxtApp()
    const { $can, $goTo } = useHelpers(emit)

    const dialogForm = ref(false)
    const readonly = ref(false)
    const currentPage = ref(1)
    const currentSearch = ref('')
    const filtersMenu = ref(false)
    const selectedItem = ref(null)
    const appendQuery = ref({ [props.searchKey]: '' })
    const filtersOptionsSlotBindings = ref({
        close: () => (filtersMenu.value = false),
        clearSearch: () => {
            currentSearch.value = ''
            appendQuery.value[props.searchKey] = ''
        }
    })

    const headerItems = computed(() => {
        if (!hasItemActions.value) {
            return props.headers
        }

        return props.headers.concat([
            { title: t('actions'), key: 'actions', sortable: false },
        ])
    })

    const hasItemActions = computed(() => Boolean(slots['append-actions'] ||
        enabledActions.value.filter(action => action !== 'store').length
    ))

    const hasTopActions = computed(() => Boolean(
        actionCanBeRendered(null, 'store') ||
            props.filters.enabled ||
            slots.filters ||
            slots['append-top-actions']
    ))

    const dialogFormComponent = computed(() => {
        if (!props.dialogFormComponentPath || slots.form) {
            return
        }

        const path = `../${props.dialogFormComponentPath}.vue`;

        return defineAsyncComponent(() => forms[path]());
    })

    const itemId = computed(() => attrs['item-key'] ?? attrs.itemKey ?? 'id')
    const enabledActions = computed(() => {
        const actions = []
        const values = Array.isArray(props.actions) ? props.actions : Object.keys(props.actions)

        for (const action of values) {
            actions.push(typeof action === 'object' ? action.name : action)
        }

        return actions.flat()
    })

    const itemActions = computed(() => {
        const actions = {}
        const defaults = {
            show: { text: t('btns.show'), icon: 'mdi-eye', color: 'blue' },
            update: { text: t('btns.edit'), icon: 'mdi-pencil', color: 'success' },
            delete: { text: t('btns.delete'), icon: 'mdi-delete', color: 'error' },
        }

        const onClickHandlers = {
            show: item => openDialogForm(item, true),
            update: item => openDialogForm(item),
            delete: item => deleteItem(item),
        }

        for (const action in defaults) {
            if (!enabledActions.value.includes(action)) {
                continue
            }

            const overrides = props.actions[action] ?? props.actions.find(a => a.name === action) ?? {}
            actions[action] = { ...defaults[action], ...overrides, name: action, onClick: onClickHandlers[action] }
        }

        return actions
    })

    const itemsPerPage = computed(() => props.pagination.per_page ?? attrs['items-per-page'] ?? 10)
    const useRouterForPagination = computed(() => props.paginationStrategy === 'router')
    const queryStringParams = computed(() => ({
        ...route.query,
        ...appendQuery.value,
        ...props.serverParams,
        page: currentPage.value,
    }))

    function openDialogForm (item, isReadonly = false) {
        selectedItem.value = item
        readonly.value = isReadonly
        dialogForm.value = true
    }

    function closeDialog (data = null) {
        dialogForm.value = false
        readonly.value = false
        selectedItem.value = null

        if (data) {
            const item = attrs.items.find(item => item[itemId.value] === data[itemId.value])

            if (item) {
                emit('item-updated', { item, data })
            } else {
                emit('item-created', data)
            }
        }
    }

    async function deleteItem (item) {
        const { dismiss } = await $swal.delete({ url: `${props.serverAction}/${item[itemId.value]}` })

        const index = attrs.items.findIndex(currentItem =>
            currentItem[itemId.value] === item[itemId.value]
        )

        if (!dismiss) {
            emit('item-deleted', { item, index })
        }
    }

    async function paginateFromAxios () {
        try {
            emit('paginating', { status: 'start' })

            const data = await $axios.$get(props.serverAction, { params: queryStringParams.value })

            emit('paginating', { status: 'success', data })
        } catch (e) {
            emit('paginating', { status: 'failed', error: e })
        }
    }

    function actionCanBeRendered (item, option) {
        const validation = props.actionsValidations[option] ?? {}

        return enabledActions.value.includes(option) &&
            (validation.callback?.(item) ?? true) &&
            (validation.skipPermissionCheck || $can(validation.permission || `${props.name}.${option}`))
    }

    function onPaginate (page) {
        if (useRouterForPagination.value || page === props.pagination.current_page) {
            return
        }

        paginateFromAxios()
    }

    function onInputSearchEnter () {
        if (currentSearch.value === appendQuery.value[props.searchKey]) {
            return
        }

        updateQuerySearch(currentSearch.value)
    }

    function onInputSearchCleared () {
        if (!appendQuery.value[props.searchKey]) {
            return
        }

        updateQuerySearch('')
    }

    function updateQuerySearch (search) {
        currentPage.value = 1
        appendQuery.value[props.searchKey] = search

        if (!useRouterForPagination.value) {
            return paginateFromAxios()
        }

        return $goTo({
            name: route.name,
            query: { ...route.query, [props.searchKey]: search, page: 1 }
        }, 'paginating')
    }

    watch(() => route.query, (value) => {
        if (useRouterForPagination.value) {
            appendQuery.value[props.searchKey] = currentSearch.value = (value[props.searchKey] ?? '')
        }
    })

    onMounted (() => {
        const { page, [props.searchKey]: search } = route.query

        appendQuery.value[props.searchKey] = currentSearch.value = (search ?? '')
        currentPage.value = props.pagination.current_page ?? Number(page || 1)

        if (!useRouterForPagination.value) {
            paginateFromAxios()
        }
    })
</script>
