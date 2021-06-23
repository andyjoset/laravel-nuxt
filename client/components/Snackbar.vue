<template>
    <v-snackbar
        v-model="show"
        top
        right
        :color="color"
        :timeout="timeout"
        :outlined="outlined">
        {{ message }}
        <template #action>
            <v-btn icon @click="show = false">
                <v-icon>mdi-{{ icon }}</v-icon>
            </v-btn>
        </template>
    </v-snackbar>
</template>

<script>
    export default {
        name: 'AppSnackbar',

        data: () => ({
            show: false,
            message: '',
            color: '',
            timeout: 5000,
            outlined: false,
        }),

        computed: {
            icon () {
                const icons = [
                    { name: 'info', icon: 'information' },
                    { name: 'error', icon: 'close' },
                    { name: 'success', icon: 'check' },
                    { name: 'warning', icon: 'alert' },
                ]

                return icons.find(icon => icon.name === this.color)?.icon
            },
            snackbar () {
                return this.$store.getters.snackbar
            },
        },

        created () {
            this.$store.subscribe((mutation, state) => {
                if (mutation.type === 'SHOW_SNACKBAR_MESSAGE') {
                    this.show = true
                    this.color = this.snackbar.color
                    this.message = this.snackbar.message
                    this.timeout = this.snackbar.timeout
                    this.outlined = this.snackbar.outlined
                }
            })
        },
    }
</script>
