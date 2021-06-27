<template>
    <v-overlay
        absolute
        :value="overlay.show"
        :opacity="overlay.opacity">
        <v-progress-circular indeterminate size="64" color="primary" />
    </v-overlay>
</template>

<script>
    export default {
        computed: {
            overlay () {
                return this.$store.getters.overlay
            }
        }
    }
</script>
