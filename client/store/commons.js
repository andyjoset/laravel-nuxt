
export const state = () => ({
    roles: [],
    permissions: [],
})

export const getters = {
    roles: state => state.roles,
    permissions: state => state.permissions,
}

export const mutations = {
    SET_ROLES (state, roles) {
        state.roles = roles
    },

    SET_PERMISSIONS (state, permissions) {
        state.permissions = permissions
    },
}

export const actions = {
    async fetchRoles ({ commit }) {
        try {
            const roles = await this.$axios.$get('/roles')

            commit('SET_ROLES', roles)
        } catch (e) {
        }
    },

    async fetchPermissions ({ commit }) {
        try {
            const roles = await this.$axios.$get('/permissions')

            commit('SET_PERMISSIONS', roles)
        } catch (e) {
        }
    },
}
