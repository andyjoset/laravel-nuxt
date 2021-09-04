<template>
    <v-alert v-if="isValid" v-bind="$attrs" type="info">
        Email verification required, if you didn't receive an email yet, click
        <v-btn text @click="requestEmailVerificationNotification">
            here
        </v-btn>
        to request one.

        <v-overlay absolute :value="pending">
            <v-progress-circular indeterminate size="32" color="primary" />
        </v-overlay>
    </v-alert>
</template>

<script>
    export default {
        data: () => ({
            pending: false,
            lastRequest: null,
        }),

        computed: {
            user () {
                return this.$store.getters['auth/user']
            },
            isValid () {
                if (this.user.email_verified_at === undefined) {
                    return false
                }

                const canRequest = this.lastRequest ? this.dateDiff(new Date(), this.lastRequest, 'hours') > 1 : true

                return !this.user.email_verified_at && canRequest
            },
        },

        mounted () {
            if (process.browser) {
                this.lastRequest = localStorage.last_email_veritificaion_notification
            }
        },

        methods: {
            async requestEmailVerificationNotification () {
                this.lastRequest = localStorage.last_email_veritificaion_notification

                if (!this.isValid) {
                    return this.$notify({
                        color: 'error',
                        message: this.user.email_verified_at
                            ? 'Your email is already verified!'
                            : 'We just sent you a verification link, please use it or try requesting another later!',
                    })
                }

                this.pending = true

                try {
                    await this.$auth.requestEmailVerificationNotification()

                    this.lastRequest = localStorage.last_email_veritificaion_notification = new Date()

                    this.$notify('We have e-mailed your verification link!')
                } catch (e) {}

                this.pending = false
            },
        },
    }
</script>
