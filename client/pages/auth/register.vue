<template>
    <app-form
        :form="form"
        :action="action"
        :disabled="form.busy"
        :title="$t('register')"
        icon="mdi-card-account-details"
        :heading-options="formHeadingOptions"
        :container-options="formContainerOptions">
        <v-alert v-if="form.errors.any()" type="error" dense dismissible>
            {{ form.errors.first() }}
        </v-alert>

        <v-text-field
            v-model="form.name"
            autofocus
            class="my-4"
            prepend-icon="mdi-face"
            :label="$tc('labels.name')" />

        <v-text-field
            v-model="form.email"
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
                        {{ $t('btns.submit') }}
                    </v-btn>
                    <br>
                    <v-btn
                        text
                        rounded
                        x-small
                        color="primary"
                        :disabled="form.busy"
                        :to="{ name: 'login' }">
                        {{ $t('login') }}
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

        middleware: 'guest',

        data: vm => ({
            action: form => vm.$auth.register(form),
            formHeadingOptions: {
                class: 'mt-n10'
            },
            formContainerOptions: {
                elevation: 12,
                maxWidth: 450,
                class: 'mx-auto my-12',
            },
            form: vm.$vform.make({
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
            })
        }),

        head: vm => ({
            title: vm.$t('register'),
        }),
    }
</script>
