<template>
    <v-footer app style="padding: 0 0;">
        <v-toolbar flat density="compact" color="primary">
            <lang-switcher v-if="$config.public.isMultiLang" />
            <dark-mode />
            <v-btn
                v-for="link in links"
                :key="`footer_${link.route}_link`"
                size="small"
                class="mx-0 white--text"
                :to="{ name: link.route }"
                :variant="routeIs(link.route) ? 'plain' : 'text'">
                <v-icon size="18px" class="mr-2 white--text" :icon="link.icon" />
                {{ link.text }}
            </v-btn>

            <template #append>
                <v-toolbar-title class="d-xs-none">
                    {{ new Date().getFullYear() }} — {{ $config.public.appName }}
                </v-toolbar-title>
            </template>
        </v-toolbar>
    </v-footer>
</template>

<script setup>
    import DarkMode from '~/components/DarkMode'
    import useHelpers from '~/composables/helpers'
    import LangSwitcher from '~/components/LangSwitcher'

    const { t } = useI18n()
    const { routeIs } = useHelpers()

    const links = computed (() => [
        { text: t('contact'), icon: 'mdi-phone', route: 'contact' },
        { text: t('about'), icon: 'mdi-information-outline', route: 'about' },
    ])
</script>
