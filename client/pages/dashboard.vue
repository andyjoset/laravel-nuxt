<template>
    <v-row>
        <v-col align="center" justify="center">
            <img
                src="/v.png"
                alt="Vuetify.js"
                class="mb-2">
            <blockquote
                v-if="user"
                v-t="{ path: 'dashboard.welcome', args: [user.name] }"
                class="blockquote" />
        </v-col>
    </v-row>
</template>

<script>
    export default {
        middleware: ['auth'],

        head: vm => ({
            title: vm.$t('dashboard.title'),
        }),

        computed: {
            user () {
                return this.$store.getters['auth/user']
            },
        },
    }
</script>
