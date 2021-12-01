<template>
    <app-form
        :form="form"
        :action="action"
        icon="mdi-lock-open"
        :disabled="form.busy"
        :title="$t('request_password_reset')"
        v-on="$listeners"
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

        <template #[`actions.save`]>
            <v-btn
                type="submit"
                color="primary"
                :loading="form.busy">
                <v-icon class="mr-1">mdi-check-circle</v-icon> {{ $t('btns.submit') }}
            </v-btn>
        </template>
    </app-form>
</template>

<script>
    import HasForm from '~/components/mixins/HasForm'

    export default {
        mixins: [HasForm],

        data: vm => ({
            action: form => vm.$auth.forgotPassword(form, 'forgot'),
            form: vm.$vform.make({
                email: '',
            })
        }),

        methods: {
            onFormSuccess (data) {
                this.clearForm()
                this.$notify(data.message)
            },
        }
    }
</script>
