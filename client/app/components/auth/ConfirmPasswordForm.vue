<template>
    <app-form
        :form="form"
        :action="action"
        :disabled="form.busy"
        v-bind="formOptions"
        v-on="$attrs">
        <span class="text-caption" v-text="$t('protected_area')" />

        <v-text-field
            v-model="form.password"
            class="mt-8"
            type="password"
            variant="underlined"
            :label="$t('labels.enter_password')"
            :error="form.errors.has('password')"
            :error-messages="form.errors.get('password')" />

        <template #actions>
            <v-card-actions class="text-caption mt-6">
                <v-col cols="12" class="text-center">
                    <v-btn
                        type="submit"
                        dark
                        block
                        rounded
                        color="primary"
                        variant="elevated"
                        :loading="form.busy">
                        {{ $t('btns.confirm') }}
                    </v-btn>
                </v-col>
            </v-card-actions>
        </template>
    </app-form>
</template>

<script setup>
    import useForm from '~/composables/form'

    const attrs = useAttrs()
    const { $auth } = useNuxtApp()
    const { form } = useForm({ password: null })
    const action = form => $auth.confirmPassword(form)

    const formHeadingOptions = {
        class: 'mx-auto mt-n10 rounded-circle',
        rounded: true,
        height: 90,
        width: 90,
    }

    const formContainerOptions = {
        elevation: 12,
        maxWidth: 230,
        class: 'mx-auto my-12',
    }

    const formIconOptions = {
        size: '250%',
        class: 'ml-n3 mt-n3'
    }

    const formOptions = computed(() => ({
        icon: 'mdi-lock',
        hideDivider: true,
        iconOptions: formIconOptions,
        headingOptions: formHeadingOptions,
        containerOptions: formContainerOptions,
        ...attrs,
    }))
</script>
