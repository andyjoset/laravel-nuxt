<template>
    <v-data-table
        hide-default-footer
        density="comfortable"
        v-bind="$attrs"
        class="app-table elevation-2 px-2">
        <!-- eslint-disable vue/no-unused-vars -->
        <template v-for="(_, name) in $slots" #[name]="slotProps">
            <slot :name="name" v-bind="slotProps" />
        </template>
        <!-- eslint-enable vue/no-unused-vars -->

        <template #top="slotProps">
            <slot name="heading">
                <v-card-title class="d-flex align-center mb-2 app-table-heading">
                    <v-sheet
                        rounded
                        elevation="6"
                        color="primary"
                        class="d-inline-flex app-table-floating-sheet">
                        <div class="pa-5">
                            <v-icon class="white--text" :icon="icon" />
                        </div>
                    </v-sheet>
                    <div class="text-h5 pl-3 mt-8" v-text="heading" />
                </v-card-title>
            </slot>

            <slot name="append-top" v-bind="slotProps" />
        </template>
    </v-data-table>
</template>

<script setup>
    defineProps({
        icon: {
            type: String,
            default: () => 'mdi-format-list-bulleted',
        },
        heading: {
            type: String,
            required: true,
        },
    })
</script>

<style scoped>
    .app-table {
        overflow: visible !important;
        position: relative;
    }

    .app-table-heading {
        margin-top: -36px;
        overflow: visible;
        position: relative;
        z-index: 3;
    }

    .app-table-floating-sheet {
        position: relative;
        z-index: 2;
    }

    .app-table :deep(.v-table) {
        overflow: visible !important;
    }

    .app-table :deep(.v-data-table__wrapper) {
        overflow: visible !important;
    }

    .app-table :deep(.v-data-table__top) {
        overflow: visible !important;
    }

    .app-table :deep(.v-table__wrapper) {
        overflow: auto;
    }
</style>
