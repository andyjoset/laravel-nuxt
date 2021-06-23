import Cookies from 'js-cookie'

export const state = () => ({
    snackbar: {
        message: '',
        color: '',
        outlined: false,
        timeout: 5000,
    },
    overlay: {
        show: false,
    },
})

export const getters = {
    snackbar: (state, getters, rootState) => rootState.snackbar,
    overlay: (state, getters, rootState) => rootState.overlay,
}

export const mutations = {
    SHOW_SNACKBAR_MESSAGE (state, config = {}) {
        Object.assign(state.snackbar, config)
    },

    TOGGLE_OVERLAY (state, options = {}) {
        Object.assign(state.overlay, {
            ...options,
            show: !state.overlay.show,
        })
    },
}

export const actions = {
    nuxtServerInit ({ commit }, { req }) {
    },

    nuxtClientInit ({ commit }, { $config }) {
        if (!$config.isStateful) {
            const token = Cookies.get('token')

            if (token) {
                commit('auth/SET_TOKEN', token)
            }
        }
    }
}
