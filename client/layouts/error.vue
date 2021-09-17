<template>
    <v-row align="center" justify="center">
        <v-col cols="12" sm="8" md="6">
            <div class="mt-8 text-h1">
                Error {{ error.statusCode }}
            </div>
            <div class="text-h4">
                {{ error.message }}
            </div>
            <v-btn nuxt color="primary" :to="{ name: 'home' }" class="mt-2">
                <v-icon class="mr-1">mdi-home</v-icon> {{ $t('home') }}
            </v-btn>
        </v-col>
    </v-row>
</template>

<script>
    export default {
        layout: 'empty',

        props: {
            error: {
                type: Object,
                default: null,
            }
        },

        data: () => ({
        }),

        head: vm => ({
            title: vm.$t(`errors.${vm.error.statusCode}`),
        }),
    }
</script>
