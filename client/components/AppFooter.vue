<template>
    <v-footer app padless>
        <v-toolbar dense color="primary" width="100%">
            <lang-switcher v-if="$config.isMultiLang" />
            <dark-mode />
            <v-btn
                v-for="(link, i) in links"
                :key="i"
                :to="{ name: link.route }"
                class="mx-0 white--text"
                :plain="routeIs(link.route)"
                nuxt
                small
                text>
                <v-icon size="18px" class="mr-2 white--text">
                    {{ link.icon }}
                </v-icon>
                {{ link.text }}
            </v-btn>

            <v-spacer />
            <v-toolbar-title class="mr-2 d-none d-sm-flex white--text">
                {{ new Date().getFullYear() }} — {{ $config.appName }}
            </v-toolbar-title>
        </v-toolbar>
    </v-footer>
</template>

<script>
    import DarkMode from '~/components/DarkMode'
    import LangSwitcher from '~/components/LangSwitcher'

    export default {
        components: {
            DarkMode,
            LangSwitcher,
        },

        computed: {
            links () {
                return [
                    { text: this.$t('contact'), icon: 'mdi-phone', route: 'contact' },
                    { text: this.$t('about'), icon: 'mdi-information-outline', route: 'about' },
                ]
            },
        },
    }
</script>
