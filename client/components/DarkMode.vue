<template>
    <v-btn
        icon
        size="small"
        @click="toggle">
        <v-icon
            size="20"
            :color="isActive ? '#000000' : '#FFFFFF'"
            :icon="`mdi-${isActive ? 'weather-night' : 'white-balance-sunny'}`" />
    </v-btn>
</template>

<script setup>
    import { useTheme } from 'vuetify'

    const theme = useTheme()
    const darkThemeCookie = useCookie('dark-theme', {
        default: () => false,
    })

    const isActive = computed (() => theme.current.value?.dark)
    const isUndef = computed (() => darkThemeCookie.value === undefined)

    function toggle () {
        setTheme(isUndef.value ? !getValueFromSystem() : !darkThemeCookie.value)
    }

    function setTheme (dark) {
        darkThemeCookie.value = dark
        theme.change((dark ? 'dark' : 'light') + 'Theme')
    }

    function getValueFromSystem () {
        return window.matchMedia('(prefers-color-scheme: dark)').matches
    }

    onMounted (() => {
        setTheme(isUndef.value ? getValueFromSystem() : darkThemeCookie.value)

        window.matchMedia('(prefers-color-scheme: dark)')
            .addEventListener('change', (e) => {
                if (darkThemeCookie.value) {
                    setTheme(e.matches)
                }
            })
    })
</script>
