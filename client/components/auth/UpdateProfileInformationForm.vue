<template>
    <v-form @submit.prevent="update">
        <v-text-field
            v-model="form.name"
            dense
            autofocus
            class="my-4"
            label="Name"
            :disabled="form.busy"
            prepend-icon="mdi-face"
            :error="form.errors.has('name')"
            :error-messages="form.errors.get('name')" />

        <v-spacer />
        <v-text-field
            v-model="form.email"
            dense
            type="email"
            label="Email"
            prepend-icon="mdi-email"
            :disabled="form.busy"
            :error="form.errors.has('email')"
            :error-messages="form.errors.get('email')" />

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
            @click="close">
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
            :loading="form.busy">
            <v-icon>mdi-check</v-icon>
        </v-btn>
    </v-form>
</template>

<script>
    export default {
        data: vm => ({
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

        created () {
            this.form.fill(this.user)
        },

        methods: {
            async update () {
                try {
                    await this.$auth.updateProfileInformation(this.form)

                    this.$notify('Updated successfully!')

                    this.close()
                } catch (e) {
                }
            },
            close () {
                this.$emit('close')
            }
        },
    }
</script>
