<template>
    <v-menu location="top bottom">
        <template #activator="{ props }">
            <v-btn icon dark variant="text" v-bind="props">
                <div v-bind="props">
                    <v-icon size="20" icon="mdi-translate" />
                    <v-icon size="14" class="ml-n1" icon="mdi-chevron-up" />
                </div>
            </v-btn>
        </template>

        <v-list density="compact" class="pa-0">
            <v-list-item
                v-for="locale in locales"
                :key="locale.code"
                :active="locale.code === activeLocale"
                @click="switchLang(locale.code)">
                <v-list-item-title class="text-center" v-text="locale.code.toUpperCase()" />
            </v-list-item>
        </v-list>
    </v-menu>
</template>

<script setup>
    import { useLocale as useVuetifyLocale } from 'vuetify'

    const { current: vuetifyLocale } = useVuetifyLocale()
    const { locale: activeLocale, locales, setLocale } = useI18n()

    async function switchLang(locale) {
        await setLocale(locale)
        vuetifyLocale.value = locale
    }
</script>
