<template>
    <v-overlay
        persistent
        :model-value="overlay.show"
        class="align-center justify-center">
        <v-progress-circular indeterminate size="64" color="primary" />
    </v-overlay>
</template>

<script setup>
    const { $store } = useNuxtApp()
    const overlay = computed (() => $store.overlay)
</script>
