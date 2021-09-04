<template>
    <v-alert :value="isInvalid" type="error" dismissible>
        The verification link is invalid.
    </v-alert>
</template>

<script>
    export default {
        middleware: [
            'auth',
            ({ store, redirect }) => {
                const user = store.getters['auth/user']
                if (user.email_verified_at === undefined || user.email_verified_at) {
                    return redirect({ name: 'profile.show' })
                }
            },
        ],

        data: () => ({
            isInvalid: false,
        }),

        head: () => ({
            title: 'Verify Email',
        }),

        created () {
            this.$store.commit('TOGGLE_OVERLAY')

            this.verifyEmail()
        },

        methods: {
            async verifyEmail () {
                try {
                    const url = new URL(this.$route.query.verify_url)

                    this.$axios.setBaseURL(this.$config.appUrl)

                    await this.$axios.get(url.href.replace(url.origin, ''))

                    this.$router.replace({ name: 'profile.show', query: { verified: 1 } })
                } catch (e) {
                    this.isInvalid = e?.response?.status === 403
                }

                this.$axios.setBaseURL(this.$config.apiUrl)
                this.$store.commit('TOGGLE_OVERLAY')
            },
        },
    }
</script>
