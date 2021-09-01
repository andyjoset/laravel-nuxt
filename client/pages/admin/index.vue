<template>
    <v-row align="center" justify="center">
        <v-col cols="12">
            <nuxt />
        </v-col>
    </v-row>
</template>

<script>
    export default {
        middleware: ['auth', 'permission'],
    }
</script>
