<template>
    <v-row class="align-center justify-center">
        <v-col cols="12" sm="8" md="6">
            <div class="mt-8 text-h1">
                Error {{ error.statusCode }}
            </div>
            <div class="text-h4">
                {{ error.message }}
            </div>
            <v-btn component="NuxtLink" color="primary" :to="{ name: 'home' }" class="mt-2">
                <v-icon class="mr-1">mdi-home</v-icon> {{ $t('home') }}
            </v-btn>
        </v-col>
    </v-row>
</template>

<script setup>
    const props = defineProps({
        error: {
            type: Object,
            default: null,
        },
    })

    const { t } = useI18n()

    useHead({
        title: t(`errors.${props.error.statusCode}`),
    })
</script>
