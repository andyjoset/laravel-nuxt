<template>
    <app-form
        :form="form"
        hide-heading
        hide-divider
        :action="action"
        :disabled="form.busy"
        :container-options="formContainerOptions"
        v-on="$listeners"
        @success="onFormSuccess">
        <v-text-field
            v-model="form.name"
            dense
            autofocus
            class="my-4"
            prepend-icon="mdi-face"
            :label="$tc('labels.name')"
            :error="form.errors.has('name')"
            :error-messages="form.errors.get('name')" />

        <v-spacer />
        <v-text-field
            v-model="form.email"
            dense
            type="email"
            prepend-icon="mdi-email"
            :label="$t('labels.email')"
            :error="form.errors.has('email')"
            :error-messages="form.errors.get('email')" />

        <template #actions="{ handleCancelClick, submit }">
            <v-spacer />
            <v-btn
                fab
                right
                bottom
                x-small
                absolute
                class="mr-10"
                color="error"
                :disabled="form.busy"
                @click="handleCancelClick">
                <v-icon>mdi-close</v-icon>
            </v-btn>
            <v-btn
                fab
                right
                bottom
                x-small
                absolute
                type="submit"
                color="primary"
                :loading="form.busy"
                @click="submit">
                <v-icon>mdi-check</v-icon>
            </v-btn>
        </template>
    </app-form>
</template>

<script>
    import HasForm from '~/components/mixins/HasForm'

    export default {
        mixins: [HasForm],

        data: vm => ({
            formInitialValuesProp: 'user',
            action: form => vm.$auth.updateProfileInformation(form),
            formContainerOptions: { class: 'mb-2', elevation: 0 },
            form: vm.$vform.make({
                name: '',
                email: '',
            }),
        }),

        computed: {
            user () {
                return this.$store.getters['auth/user']
            }
        },

        methods: {
            onFormSuccess () {
                this.$notify(this.$t('alerts.updated'))
            },
        },
    }
</script>
