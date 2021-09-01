<template>
    <div v-show="visible" class="d-inline-block">
        <v-switch
            v-if="isSwitch"
            class="mb-0 mt-6"
            :input-value="isActive"
            color="secundary"
            flat
            @change="toggle" />

        <v-btn
            v-else
            icon
            small
            :class="isActive ? 'mt-0' : 'mt-1'"
            @click="toggle">
            <v-icon size="20" :class="isActive ? 'black--text' : 'white--text'">
                mdi-{{ isActive ? 'weather-night' : 'white-balance-sunny' }}
            </v-icon>
        </v-btn>
    </div>
</template>

<script>
    export default {
        props: {
            type: {
                type: String,
                default: 'button',
                validator: value => ['button', 'switch'].includes(value),
            }
        },

        data: () => ({
            visible: false
        }),

        computed: {
            isActive () {
                return this.$vuetify.theme.dark
            },
            isSwitch () {
                return this.type === 'switch'
            },
        },

        created () {
            if (process.browser) {
                this.$vuetify.theme.dark = this.isUndef()
                    ? this.getValueFromSystem()
                    : this.getValueFromStorage()

                this.visible = true
            }
        },

        mounted () {
            if (process.browser) {
                window.matchMedia('(prefers-color-scheme: dark)')
                    .addEventListener('change', (e) => {
                        if (this.isUndef()) {
                            this.$vuetify.theme.dark = e.matches
                        }
                    })
            }
        },

        methods: {
            getValueFromStorage () {
                return JSON.parse(localStorage['dark-mode'] || false)
            },
            getValueFromSystem () {
                return window.matchMedia('(prefers-color-scheme: dark)').matches
            },
            toggle () {
                if (process.browser) {
                    const result = this.isUndef() ? !this.getValueFromSystem() : !this.getValueFromStorage()

                    this.$vuetify.theme.dark = localStorage['dark-mode'] = result
                }
            },
            isUndef () {
                return localStorage['dark-mode'] === undefined
            },
        },
    }
</script>
