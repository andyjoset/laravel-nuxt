<template>
    <v-form autocomplete="off" @submit.prevent="$auth.register(form)">
        <v-card
            elevation="12"
            max-width="430"
            class="mx-auto my-12">
            <v-card-title class="my-0">
                <v-sheet
                    rounded
                    width="100%"
                    elevation="6"
                    max-width="400"
                    color="primary"
                    class="overflow-hidden text-center mt-n10">
                    <v-theme-provider>
                        <div class="pa-7">
                            <span class="text-h5">
                                <v-icon>
                                    mdi-card-account-details
                                </v-icon>
                                {{ $t('register') }}
                            </span>
                        </div>
                    </v-theme-provider>
                </v-sheet>
                <v-card-text>
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
                </v-card-text>
            </v-card-title>

            <v-divider class="mx-4 my-0 pa-0" />

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
        </v-card>
    </v-form>
</template>

<script>
    import Form from 'vform'

    export default {
        middleware: 'guest',

        data: () => ({
            form: new Form({
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
