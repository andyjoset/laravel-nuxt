<template>
    <v-menu
        v-model="menu"
        :close-on-content-click="false"
        transition="scale-transition"
        :nudge-width="200"
        offset-y
        rounded
        bottom>
        <template #activator="{ on }">
            <v-btn icon large v-on="on">
                <v-avatar size="42">
                    <img :src="user.photo_url" :alt="user.name">
                </v-avatar>
            </v-btn>
        </template>
        <v-card>
            <v-list-item-content class="justify-center">
                <div class="mx-auto text-center">
                    <v-avatar size="38">
                        <img :src="user.photo_url" :alt="user.name">
                    </v-avatar>

                    <h4 class="mt-1" v-text="user.name" />
                    <p class="text-caption mt-1" v-text="user.email" />

                    <v-divider class="my-1" />
                    <v-btn depressed rounded text :to="{ name: 'profile' }">
                        <v-icon small>mdi-account</v-icon> Profile
                    </v-btn>

                    <v-divider class="mt-2 mb-1" />
                    <v-btn depressed rounded text class="mb-0" @click="$auth.logout()">
                        <v-icon small>mdi-logout</v-icon> Logout
                    </v-btn>
                </div>
            </v-list-item-content>
        </v-card>
    </v-menu>
</template>

<script>
    export default {
        data: () => ({
            menu: false,
        }),

        computed: {
            user () {
                return this.$store.getters['auth/user']
            },
        },
    }
</script>
