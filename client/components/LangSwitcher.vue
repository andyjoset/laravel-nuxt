<template>
    <v-menu
        top
        offset-y
        close-on-click
        close-on-content-click>
        <template #activator="{ on, attrs }">
            <v-btn small text dark v-bind="attrs" v-on="on">
                <div v-bind="attrs" v-on="on">
                    <v-icon size="20">mdi-translate</v-icon>
                    <v-icon size="14" class="ml-n1">mdi-chevron-up</v-icon>
                </div>
            </v-btn>
        </template>

        <v-list dense class="pa-0">
            <v-list-item
                v-for="item in locales"
                :key="item.code"
                :input-value="item.code === locale"
                @click="$i18n.setLocale(item.code)">
                <v-list-item-content>
                    <v-list-item-title class="text-center" v-text="item.code.toUpperCase()" />
                </v-list-item-content>
            </v-list-item>
        </v-list>
    </v-menu>
</template>

<script>
    export default {
        computed: {
            locale () {
                return this.$i18n.locale
            },
            locales () {
                return this.$i18n.locales
            },
        },
    }
</script>
