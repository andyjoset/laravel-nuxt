<template>
    <app-table
        :search="search"
        :loader-height="2"
        :headers="headerItems"
        :items-per-page="itemsPerPage"
        v-bind="$attrs"
        class="elevation-2 px-2">
        <!-- eslint-disable vue/no-unused-vars -->
        <template v-for="(_, slotName) in $scopedSlots" #[slotName]="slotData">
            <slot :name="slotName" v-bind="slotData" />
        </template>
        <!-- eslint-enable vue/no-unused-vars -->

        <template #append-top>
            <v-toolbar flat>
                <v-tooltip v-if="actionCanBeRendered(null, 'store')" top>
                    <template #activator="{ on }">
                        <v-btn
                            small
                            color="primary"
                            :disabled="$attrs.loading"
                            @click="dialog = true"
                            v-on="on">
                            <v-icon small v-text="actionCreate.icon || 'mdi-plus-box-multiple'" />
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
                        <template #activator="{ on: menu, attrs }">
                            <v-tooltip top>
                                <template #activator="{ on: tooltip }">
                                    <v-btn
                                        dark
                                        small
                                        color="blue"
                                        :disabled="$attrs.loading"
                                        class="ml-2"
                                        v-bind="attrs"
                                        v-on="{ ...tooltip, ...menu }">
                                        <v-icon small v-text="filters.icon || 'mdi-filter'" />
                                    </v-btn>
                                </template>
                                <span v-text="filters.text || $t('show', [$t('filters')])" />
                            </v-tooltip>
                        </template>

                        <slot name="filters.options" v-bind="filtersOptionsSlotBindings">
                            <v-card>
                                <v-list>
                                    <v-list-item>
                                        <v-list-item-content>
                                            {{ $t('filter_by') }}
                                            <v-tooltip top>
                                                <template #activator="{ on }">
                                                    <v-btn
                                                        icon
                                                        fixed
                                                        right
                                                        small
                                                        v-on="on"
                                                        @click="filtersMenu = false">
                                                        <v-icon>mdi-close</v-icon>
                                                    </v-btn>
                                                </template>
                                                <span v-t="'btns.close'" />
                                            </v-tooltip>
                                        </v-list-item-content>
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
                    v-model="search"
                    clearable
                    single-line
                    :label="$t('search')"
                    :hide-details="!search"
                    :hint="$t('search_hint')"
                    append-icon="mdi-magnify"
                    :disabled="$attrs.loading"
                    :persistent-hint="Boolean(search)"
                    @keydown.enter="onInputSearchEnter"
                    @click:clear="onInputSearchCleared" />

                <v-dialog
                    v-if="dialogFormComponentPath || $slots.form"
                    v-model="dialog"
                    persistent
                    v-bind="{ 'max-width': '500px', ...dialogFormProps }">
                    <slot name="form">
                        <component
                            :is="dialogForm"
                            v-bind="{ ...formProps, [itemName]: selectedItem, readonly }"
                            @success="closeDialog"
                            @cancel="closeDialog(null)"
                            @close="closeDialog(null)" />
                    </slot>
                </v-dialog>
            </v-toolbar>
        </template>

        <template #[`item.actions`]="slotData">
            <client-only>
                <template v-for="action in itemActions">
                    <v-tooltip
                        v-if="actionCanBeRendered(slotData.item, action.name)"
                        :key="`${action.name}_${slotData.item[itemId]}`"
                        top>
                        <template #activator="{ on }">
                            <v-icon
                                small
                                :color="action.color"
                                :disabled="action.disabled || $attrs.loading"
                                @click="action.onClick(slotData.item)"
                                v-on="on"
                                v-text="action.icon" />
                        </template>
                        <span v-text="action.text" />
                    </v-tooltip>
                </template>
            </client-only>

            <slot name="append-actions" v-bind="slotData" />
        </template>

        <template v-if="!$slots.footer" #footer>
            <v-divider />
            <pagination
                ref="pagination"
                v-model="currentPage"
                :config="pagination"
                :use-router="useRouter"
                :disabled="$attrs.loading"
                :append-query="appendQuery"
                v-bind="pagination.props"
                v-on="$listeners"
                @input="onPaginate" />
        </template>
    </app-table>
</template>

<script>
    export default {
        props: {
            name: {
                type: String,
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
        },

        data: vm => ({
            search: '',
            dialog: false,
            readonly: false,
            currentPage: 1,
            filtersMenu: false,
            selectedItem: null,
            appendQuery: { [vm.searchKey]: '' },
            filtersOptionsSlotBindings: {
                close: () => (vm.filtersMenu = false),
                clearSearch: () => {
                    vm.search = ''
                    vm.appendQuery[vm.searchKey] = ''
                }
            },
        }),

        computed: {
            headerItems () {
                if (!this.hasItemActions) {
                    return this.$attrs.headers
                }

                return this.$attrs.headers.concat([
                    { text: this.$t('actions'), value: 'actions', sortable: false },
                ])
            },
            hasItemActions () {
                return Boolean(
                    this.$scopedSlots['append-actions'] ||
                        this.enabledActions.filter(action => action !== 'store').length
                )
            },
            hasTopActions () {
                return Boolean(
                    this.actionCanBeRendered(null, 'store') ||
                        this.filters.enabled ||
                        this.$slots.filters ||
                        this.$slots['append-top-actions']
                )
            },
            dialogForm () {
                if (!this.dialogFormComponentPath || this.$slots.form) {
                    return
                }

                return () => import(`~/components/${this.dialogFormComponentPath}`)
            },
            itemId () {
                return this.$attrs['item-key'] ?? this.$attrs.itemKey ?? 'id'
            },
            enabledActions () {
                const actions = []
                const values = Array.isArray(this.actions) ? this.actions : Object.keys(this.actions)

                for (const action of values) {
                    actions.push(typeof action === 'object' ? action.name : action)
                }

                return actions.flat()
            },
            itemActions () {
                const actions = {}
                const defaults = {
                    show: { text: this.$t('btns.show'), icon: 'mdi-eye', color: 'blue' },
                    update: { text: this.$t('btns.edit'), icon: 'mdi-pencil', color: 'success' },
                    delete: { text: this.$t('btns.delete'), icon: 'mdi-delete', color: 'error' },
                }

                const onClickHandlers = {
                    show: item => this.openDialog(item, true),
                    update: item => this.openDialog(item),
                    delete: item => this.deleteItem(item),
                }

                for (const action in defaults) {
                    if (!this.enabledActions.includes(action)) {
                        continue
                    }

                    const overrides = this.actions[action] ?? this.actions.find(a => a.name === action) ?? {}
                    actions[action] = { ...defaults[action], ...overrides, name: action, onClick: onClickHandlers[action] }
                }

                return actions
            },
            itemsPerPage () {
                return this.pagination.per_page ?? this.$attrs['items-per-page'] ?? 10
            },
            queryStringParams () {
                return {
                    ...this.$route.query,
                    ...this.appendQuery,
                    ...this.serverParams,
                    page: this.currentPage,
                }
            },
            useRouter () {
                return this.paginationStrategy === 'router'
            },
        },

        watch: {
            'pagination.current_page' (val) {
                this.currentPage = val
            },
            '$route.query' (val) {
                if (this.useRouter) {
                    this.appendQuery[this.searchKey] = this.search = (val[this.searchKey] ?? '')
                }
            },
        },

        created () {
            const { page, [this.searchKey]: search } = this.$route.query

            this.appendQuery[this.searchKey] = this.search = (search ?? '')
            this.currentPage = this.pagination.current_page ?? Number(page || 1)

            if (!this.useRouter) {
                this.paginateFromAxios()
            }
        },

        methods: {
            openDialog (item, readonly = false) {
                this.selectedItem = item
                this.readonly = readonly
                this.dialog = true
            },
            closeDialog (data = null) {
                this.dialog = false
                this.readonly = false
                this.selectedItem = null

                if (data) {
                    const item = this.$attrs.items.find(item => item[this.itemId] === data[this.itemId])

                    item ? this.$emit('item-updated', { item, data }) : this.$emit('item-created', data)
                }
            },
            async deleteItem (item) {
                const { dismiss } = await this.$swal.delete({ url: `${this.serverAction}/${item[this.itemId]}` })

                const index = this.$attrs.items.findIndex(currentItem =>
                    currentItem[this.itemId] === item[this.itemId]
                )

                if (!dismiss) {
                    this.$emit('item-deleted', { item, index })
                }
            },
            async paginateFromAxios () {
                try {
                    this.$emit('paginating', { status: 'start' })

                    const data = await this.$axios.$get(this.serverAction, { params: this.queryStringParams })

                    this.$emit('paginating', { status: 'success', data })
                } catch (e) {
                    this.$emit('paginating', { status: 'failed', error: e })
                }
            },
            actionCanBeRendered (item, option) {
                const validation = this.actionsValidations[option] ?? {}

                return this.enabledActions.includes(option) &&
                    (validation.callback?.(item) ?? true) &&
                    (validation.skipPermissionCheck || this.$can(validation.permission || `${this.name}.${option}`))
            },
            onPaginate (page) {
                if (this.useRouter || page === this.pagination.current_page) {
                    return
                }

                this.paginateFromAxios()
            },
            onInputSearchEnter () {
                if (this.search === this.appendQuery[this.searchKey]) {
                    return
                }

                this.updateQuerySearch(this.search)
            },
            onInputSearchCleared () {
                if (!this.appendQuery[this.searchKey]) {
                    return
                }

                this.updateQuerySearch('')
            },
            updateQuerySearch (search) {
                this.currentPage = 1
                this.appendQuery[this.searchKey] = search

                if (!this.useRouter) {
                    return this.paginateFromAxios()
                }

                return this.$goTo({
                    name: this.$route.name,
                    query: { ...this.$route.query, [this.searchKey]: search, page: 1 }
                }, 'paginating')
            },
        },
    }
</script>
