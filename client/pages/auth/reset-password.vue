<template>
    <app-form
        :form="form"
        :action="action"
        :disabled="form.busy"
        icon="mdi-lock-open"
        :title="$t('reset_password')"
        :heading-options="formHeadingOptions"
        :container-options="formContainerOptions"
        @success="onFormSuccess">
        <v-alert v-if="form.errors.any()" type="error" dense dismissible>
            {{ form.errors.first() }}
        </v-alert>

        <v-text-field
            v-model="form.email"
            autofocus
            class="my-4"
            type="email"
            prepend-icon="mdi-email"
            :label="$t('labels.email')" />

        <v-text-field
            v-model="form.password"
            type="password"
            prepend-icon="mdi-lock"
            :label="$t('labels.password')" />

        <v-text-field
            v-model="form.password_confirmation"
            type="password"
            prepend-icon="mdi-lock"
            :label="$t('labels.password_confirmation')" />

        <template #actions>
            <v-card-actions class="text-caption">
                <v-col cols="12" class="text-center">
                    <v-btn
                        type="submit"
                        dark
                        block
                        color="primary"
                        :loading="form.busy">
                        {{ $t('reset') }}
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

        middleware: ['guest'],

        data: vm => ({
            status: '',
            action: form => vm.$auth.forgotPassword(form, 'reset'),
            formHeadingOptions: {
                class: 'mt-n10'
            },
            formContainerOptions: {
                elevation: 12,
                maxWidth: 450,
                class: 'mx-auto my-12',
            },
            form: vm.$vform.make({
                token: '',
                email: '',
                password: '',
                password_confirmation: '',
            })
        }),

        head: vm => ({
            title: vm.$t('reset_password'),
        }),

        created () {
            this.form.update({
                email: this.$route.query.email,
                token: this.$route.params.token,
            })
        },

        methods: {
            onFormSuccess (data) {
                this.clearForm()

                this.$notify(data.message)

                this.$router.replace({ name: 'login' })
            },
        }
    }
</script>
