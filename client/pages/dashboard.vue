<template>
    <v-row>
        <v-col align="center" justify="center">
            <img
                src="/v.png"
                alt="Vuetify.js"
                class="mb-2">
            <blockquote v-if="user" class="blockquote">
                Welcome {{ user.name }} you're logged in!
            </blockquote>
        </v-col>
    </v-row>
</template>

<script>
    export default {
        middleware: ['auth'],

        head: () => ({
            title: 'Dashboard'
        }),

        computed: {
            user () {
                return this.$store.getters['auth/user']
            },
        },
    }
</script>
