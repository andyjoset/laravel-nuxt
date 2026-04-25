import { Form, Errors } from 'vform'

export default defineNuxtPlugin((nuxtApp) => {
    Errors.prototype.first = function () {
        return this.flatten()[0]
    }

    Form.axios = nuxtApp.$axios

    nuxtApp.provide('vform', Form)
})
