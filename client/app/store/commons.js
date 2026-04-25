import { defineStore } from 'pinia'

const { $axios } = useNuxtApp()

export const useCommonStore = defineStore('commons', {
    state: () => ({
        roles: [],
        permissions: [],
        fetchingPermissions: false,
    }),

    actions: {
        setRoles (roles) {
            this.roles = roles
        },
        setPermissions (permissions) {
            this.permissions = permissions
        },
        async fetchRoles () {
            try {
                const roles = await $axios.$get('/roles')

                this.setRoles(roles)
            } catch (e) {
            }
        },
        async fetchPermissions () {
            this.fetchingPermissions = true

            try {
                const roles = await $axios.$get('/permissions')

                this.setPermissions(roles)
            } catch (e) {
            }

            this.fetchingPermissions = false
        },
    },
})
