<template>
    <v-form
        :disabled="disabled"
        v-bind="$attrs"
        @submit.prevent="submit">
        <v-card v-bind="containerOptions">
            <v-card-title v-if="!hideHeading" class="my-0">
                <slot name="heading">
                    <v-sheet class="text-center" v-bind="headingProps">
                        <v-theme-provider>
                            <div class="pa-7">
                                <span class="text-h5">
                                    <v-icon v-bind="iconOptions" v-text="icon" /> {{ title }}
                                </span>
                            </div>
                        </v-theme-provider>
                    </v-sheet>
                </slot>
            </v-card-title>

            <v-card-text>
                <slot />
            </v-card-text>

            <v-divider v-if="!hideDivider" class="mx-4 my-0 pa-0" />

            <slot name="actions" v-bind="{ handleCloseClick, handleCancelClick, submit }">
                <v-card-actions class="mx-2">
                    <v-spacer />
                    <slot name="actions.close" :handleClick="handleCloseClick">
                        <v-btn
                            v-if="$attrs.readonly"
                            color="error"
                            :disabled="disabled"
                            @click.prevent="handleCloseClick">
                            <v-icon class="mr-1">mdi-close-circle</v-icon> {{ $t('btns.close') }}
                        </v-btn>
                    </slot>

                    <slot name="actions.cancel" :handleClick="handleCancelClick">
                        <v-btn
                            v-if="!$attrs.readonly && !hideCancelButton"
                            color="error"
                            :disabled="disabled"
                            @click.prevent="handleCancelClick">
                            <v-icon class="mr-1">mdi-close-circle</v-icon> {{ $t('btns.cancel') }}
                        </v-btn>
                    </slot>

                    <slot name="actions.save" :handleClick="submit">
                        <v-btn
                            v-if="!$attrs.readonly"
                            type="submit"
                            color="primary"
                            :loading="loading">
                            <v-icon class="mr-1">mdi-check-circle</v-icon> {{ $t('btns.save') }}
                        </v-btn>
                    </slot>

                    <slot name="append-actions" />
                </v-card-actions>
            </slot>
        </v-card>
    </v-form>
</template>

<script>
    import { Form } from 'vform'

    export default {
        props: {
            form: {
                type: Object,
                required: true,
                validator: value => value instanceof Form,
            },
            action: {
                type: [String, Function],
                required: true,
            },
            method: {
                type: String,
                default: 'post',
                validator: value => ['get', 'post', 'put', 'patch', 'delete'].includes(value),
            },
            hideHeading: {
                type: Boolean,
                default: false,
            },
            hideDivider: {
                type: Boolean,
                default: false,
            },
            hideCancelButton: {
                type: Boolean,
                default: false,
            },
            title: {
                type: String,
                default: null,
            },
            icon: {
                type: String,
                default: 'mdi-clipboard-text',
            },
            iconOptions: {
                type: Object,
                default: () => ({}),
            },
            headingOptions: {
                type: Object,
                default: () => ({}),
            },
            containerOptions: {
                type: Object,
                default: () => ({}),
            },
        },

        computed: {
            disabled () {
                return Boolean(this.loading || this.$attrs.disabled)
            },
            loading () {
                return this.form.busy
            },
            headingProps () {
                return {
                    width: '100%',
                    rounded: true,
                    elevation: 6,
                    color: 'primary',
                    maxWidth: '100%',
                    ...this.headingOptions,
                }
            },
        },

        methods: {
            async submit () {
                this.$emit('submit')

                try {
                    const response = typeof this.action === 'function'
                        ? await this.action(this.form)
                        : await this.form[this.method](this.action)

                    this.$emit('success', response?.data ?? {})
                } catch (e) {
                    this.$emit('failed', e)
                }
            },
            handleCloseClick () {
                this.$emit('close')
            },
            handleCancelClick () {
                this.$emit('cancel')
            },
        },
    }
</script>
