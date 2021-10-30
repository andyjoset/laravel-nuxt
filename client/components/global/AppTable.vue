<template>
    <v-data-table
        hide-default-footer
        v-bind="$attrs"
        class="elevation-2 px-2">
        <!-- eslint-disable vue/no-unused-vars -->
        <template v-for="(_, name) in $scopedSlots" #[name]="slotData">
            <slot :name="name" v-bind="slotData" />
        </template>
        <!-- eslint-enable vue/no-unused-vars -->

        <template #top="slotData">
            <slot name="heading">
                <v-card-title class="align-start mb-2">
                    <v-sheet
                        rounded
                        elevation="6"
                        color="primary"
                        max-width="100%"
                        class="mt-n9">
                        <div class="pa-5">
                            <v-icon class="white--text" v-text="icon" />
                        </div>
                    </v-sheet>
                    <div class="text-h5 pl-3" v-text="heading" />
                </v-card-title>
            </slot>

            <slot name="append-top" v-bind="slotData" />
        </template>
    </v-data-table>
</template>

<script>
    export default {
        props: {
            icon: {
                type: String,
                default: () => 'mdi-format-list-bulleted',
            },
            heading: {
                type: String,
                required: true,
            },
        },
    }
</script>
