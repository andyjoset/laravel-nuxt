<template>
    <confirm-password-form @success="onFormSuccess" />
</template>

<script>
    import ConfirmPasswordForm from '~/components/auth/ConfirmPasswordForm'

    export default {
        components: {
            ConfirmPasswordForm,
        },

        middleware: [
            'auth',
            ({ $auth, query, redirect }) => {
                if (!query.redirect) {
                    return redirect('/')
                }
            },
        ],

        head: vm => ({
            title: vm.$t('confirm_password'),
        }),

        created () {
            if (this.$store.state.overlay.show) {
                this.$store.commit('TOGGLE_OVERLAY')
            }
        },

        methods: {
            async onFormSuccess (data) {
                this.$store.commit('TOGGLE_OVERLAY')

                try {
                    await this.$router.replace(this.$route.query.redirect)
                } catch (e) {
                }

                this.$store.commit('TOGGLE_OVERLAY')
            },
        },
    }
</script>
