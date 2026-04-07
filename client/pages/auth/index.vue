<template>
    <v-row align="center" justify="center">
        <v-col cols="12" sm="8" md="6">
            <nuxt-page />
        </v-col>
    </v-row>
</template>

<script setup>
    definePageMeta({
        layout: 'auth',
        middleware: ['auth'],
    })
</script>
