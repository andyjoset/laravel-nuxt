import Cookies from 'js-cookie'

export const state = () => ({
    user: null,
    token: null,
})

export const getters = {
    user: state => state.user,
    token: state => state.token,
    check: state => Boolean(state.user),
}

export const mutations = {
    SET_USER (state, user) {
        state.user = user
    },

    SET_TOKEN (state, token) {
        state.token = token
    },

    CLEAR (state) {
        state.user = null
        state.token = null

        if (process.browser) {
            localStorage.loggedOut = true
        }
    },

    UPDATE_USER (state, user) {
        Object.assign(state.user, user)
    },
}

export const actions = {
    async fetchUser ({ commit }) {
        try {
            const user = await this.$axios.$get('/user')

            commit('SET_USER', user)
        } catch (e) {
            commit('CLEAR')
            Cookies.remove('token')
        }
    },
}
