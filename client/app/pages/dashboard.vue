<template>
    <v-row>
        <v-col class="text-center">
            <img
                src="/v.png"
                alt="Vuetify.js"
                class="mb-2">
            <blockquote
                v-if="user"
                class="blockquote"
                v-text="$t('dashboard.welcome', [user.name])" />
        </v-col>
    </v-row>
</template>

<script setup>
    import { useAuthStore } from '~/store/auth'

    const { t } = useI18n()
    const authStore = useAuthStore()
    const user = computed(() => authStore.user)

    useHead({
        title: t('dashboard.title'),
    })
</script>
