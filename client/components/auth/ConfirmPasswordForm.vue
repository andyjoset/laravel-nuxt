<template>
    <app-form
        :form="form"
        :action="action"
        :disabled="form.busy"
        v-bind="formOptions"
        v-on="$listeners">
        <span v-t="'protected_area'" class="text-caption" />

        <v-text-field
            v-model="form.password"
            class="mt-8"
            type="password"
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
                        :loading="form.busy">
                        {{ $t('btns.confirm') }}
                    </v-btn>
                </v-col>
            </v-card-actions>
        </template>
    </app-form>
</template>

<script>
    import HasForm from '~/components/mixins/HasForm'

    export default {
        mixins: [HasForm],

        data: vm => ({
            action: form => vm.$auth.confirmPassword(form),
            formHeadingOptions: {
                class: 'mx-auto mt-n10 rounded-circle',
                rounded: true,
                height: 90,
                width: 90,
            },
            formContainerOptions: {
                elevation: 12,
                maxWidth: 230,
                class: 'mx-auto my-12',
            },
            formIconOptions: {
                size: '250%',
                class: 'ml-n3 mt-n3'
            },
            form: vm.$vform.make({
                password: null,
            })
        }),

        computed: {
            formOptions () {
                return {
                    icon: 'mdi-lock',
                    hideDivider: true,
                    iconOptions: this.formIconOptions,
                    headingOptions: this.formHeadingOptions,
                    containerOptions: this.formContainerOptions,
                    ...this.$attrs,
                }
            },
        },
    }
</script>
