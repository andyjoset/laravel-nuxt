<template>
    <v-alert v-if="isValid" v-bind="$attrs" type="info">
        <i18n path="email_verification_required.text">
            <template #action>
                <v-btn text @click="requestEmailVerificationNotification">
                    {{ $t('email_verification_required.action_text') }}
                </v-btn>
            </template>
        </i18n>

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
            this.lastRequest = localStorage.last_email_veritificaion_notification
        },

        methods: {
            async requestEmailVerificationNotification () {
                this.lastRequest = localStorage.last_email_veritificaion_notification

                if (!this.isValid) {
                    return this.$notify({
                        color: 'error',
                        message: this.$t(this.user.email_verified_at
                            ? 'email_already_verified'
                            : 'verification_link_recently_sent'
                        ),
                    })
                }

                this.pending = true

                try {
                    await this.$auth.requestEmailVerificationNotification()

                    this.lastRequest = localStorage.last_email_veritificaion_notification = new Date()

                    this.$notify(this.$t('verification_link_sent'))
                } catch (e) {}

                this.pending = false
            },
        },
    }
</script>
