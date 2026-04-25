<template>
    <v-form
        :disabled="disabled"
        v-bind="$attrs"
        @submit.prevent="submit">
        <v-card
            v-bind="containerOptions"
            class="app-form-card"
            :class="{ 'app-form-card--floating-heading': hasFloatingHeading }">
            <v-card-title
                v-if="!hideHeading"
                class="my-0"
                :class="{ 'app-form-heading--floating app-form-heading': hasFloatingHeading }"
                :style="headingContainerStyles">
                <slot name="heading">
                    <v-sheet class="text-center" v-bind="headingProps">
                        <div class="pa-7">
                            <span class="text-h5">
                                <v-icon v-bind="iconOptions" :icon="icon" /> {{ title }}
                            </span>
                        </div>
                    </v-sheet>
                </slot>
            </v-card-title>

            <v-card-text :style="cardTextStyles">
                <slot />
            </v-card-text>

            <v-divider v-if="!hideDivider" class="mx-4 my-0 pa-0" />

            <slot name="actions" v-bind="{ handleCloseClick, handleCancelClick, submit }">
                <v-card-actions class="mx-2">
                    <v-spacer />
                    <slot name="actions.close" :handle-click="handleCloseClick">
                        <v-btn
                            v-if="$attrs.readonly"
                            color="error"
                            :disabled="disabled"
                            @click.prevent="handleCloseClick">
                            <v-icon class="mr-1" icon="mdi-close-circle" /> {{ $t('btns.close') }}
                        </v-btn>
                    </slot>

                    <slot name="actions.cancel" :handle-click="handleCancelClick">
                        <v-btn
                            v-if="!$attrs.readonly && !hideCancelButton"
                            color="error"
                            :disabled="disabled"
                            @click.prevent="handleCancelClick">
                            <v-icon class="mr-1" icon="mdi-close-circle" /> {{ $t('btns.cancel') }}
                        </v-btn>
                    </slot>

                    <slot name="actions.save" :handle-click="submit">
                        <v-btn
                            v-if="!$attrs.readonly"
                            type="submit"
                            color="primary"
                            :loading="loading">
                            <v-icon class="mr-1" icon="mdi-check-circle" /> {{ $t('btns.save') }}
                        </v-btn>
                    </slot>

                    <slot name="append-actions" />
                </v-card-actions>
            </slot>
        </v-card>
    </v-form>
</template>

<script setup>
    import { Form } from 'vform'

    const props = defineProps({
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
        floatingHeading: {
            type: Boolean,
            default: true,
        },
        containerOptions: {
            type: Object,
            default: () => ({}),
        },
    })

    const emit = defineEmits(['submit', 'success', 'failed', 'close', 'cancel'])
    const attrs = useAttrs()
    const loading = computed (() => props.form.busy)
    const disabled = computed (() => Boolean(loading.value || attrs.disabled))
    const headingTopOffset = computed (() => {
        const options = props.headingOptions ?? {}

        if (typeof options.class === 'string') {
            const classMatch = options.class.match(/(?:^|\s)mt-n(\d+)(?:\s|$)/)

            if (classMatch) {
                return Number(classMatch[1]) * 4
            }
        }

        if (typeof options.style === 'string') {
            const styleMatch = options.style.match(/margin-top\s*:\s*(-?\d+)px/i)

            if (styleMatch) {
                return Math.abs(Number(styleMatch[1]))
            }
        }

        return 0
    })

    const sanitizedHeadingOptions = computed (() => {
        const options = { ...props.headingOptions }

        if (typeof options.class === 'string') {
            options.class = options.class
                .replace(/(?:^|\s)mt-n\d+(?=\s|$)/g, ' ')
                .replace(/\s+/g, ' ')
                .trim()
        }

        if (typeof options.style === 'string') {
            options.style = options.style
                .replace(/margin-top\s*:\s*-?\d+px;?/gi, '')
                .trim()

            if (!options.style) {
                delete options.style
            }
        }

        return options
    })

    const hasFloatingHeading = computed (() => Boolean(props.floatingHeading && !props.hideHeading && headingTopOffset.value > 0))
    const floatingHeadingOffset = computed (() => hasFloatingHeading.value ? 40 : 0)

    const headingContainerStyles = computed (() => {
        const options = {}
        if (hasFloatingHeading.value) {
            options.top = `-${floatingHeadingOffset.value}px`
        }

        return options
    })

    const cardTextStyles = computed (() => {
        const options = {}
        if (hasFloatingHeading.value) {
            options.paddingTop = `${floatingHeadingOffset.value + 8}px`
        }

        return options
    })

    const headingProps = computed (() => ({
        width: '100%',
        rounded: true,
        elevation: 6,
        color: 'primary',
        maxWidth: '100%',
        ...sanitizedHeadingOptions.value,
    }))

    async function submit () {
        emit('submit')

        try {
            const response = typeof props.action === 'function'
                ? await props.action(props.form)
                : await props.form[props.method](props.action)

            emit('success', response?.data ?? {})
        } catch (e) {
            emit('failed', e)
        }
    }

    function handleCloseClick () {
        emit('close')
    }

    function handleCancelClick () {
        emit('cancel')
    }
</script>

<style scoped>
    .app-form-card {
        overflow: visible;
        position: relative;
    }

    .app-form-heading {
        min-height: 0;
        padding: 0 16px;
        overflow: visible;
        position: relative;
        z-index: 2;
    }

    .app-form-heading--floating {
        left: 0;
        margin: 0;
        position: absolute;
        right: 0;
    }
</style>
