<template>
    <v-row align="center" justify="center">
        <v-col cols="12" sm="8" md="6">
            <nuxt />
        </v-col>
    </v-row>
</template>

<script>
    export default {
        layout: 'auth',

        middleware: ['guest'],
    }
</script>
