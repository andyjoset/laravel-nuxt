<template>
    <v-menu
        v-model="menu"
        :close-on-content-click="false"
        transition="scale-transition"
        location="bottom">
        <template #activator="{ props }">
            <v-btn icon size="large" v-bind="props">
                <v-avatar size="42">
                    <v-img :src="user.photo_url" :alt="user.name" cover />
                </v-avatar>
            </v-btn>
        </template>
        <v-card class="text-center mx-auto">
            <v-list width="250">
                <v-avatar size="38">
                    <v-img :src="user.photo_url" :alt="user.name" cover />
                </v-avatar>

                <h4 class="mt-1" v-text="user.name" />
                <p class="text-caption mt-1" v-text="user.email" />

                <v-divider class="my-1" />
                <v-btn rounded variant="text" :to="{ name: 'profile' }">
                    <v-icon icon="mdi-account" /> {{ $t('profile.title') }}
                </v-btn>

                <v-divider class="mt-2 mb-1" />
                <v-btn rounded variant="text" class="mb-0" @click="$auth.logout()">
                    <v-icon icon="mdi-logout" /> {{ $t('logout') }}
                </v-btn>
            </v-list>
        </v-card>
    </v-menu>
</template>

<script setup>
    import { useAuthStore } from '~/store/auth'

    const authStore = useAuthStore()
    const menu = ref(false)
    const user = computed (() => authStore.user)
</script>
