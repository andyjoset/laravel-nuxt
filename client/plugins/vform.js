import { Form, Errors } from 'vform'

export default function ({ $axios, redirect }, inject) {
    Errors.prototype.first = function () {
        return this.flatten()[0]
    }

    Form.axios = $axios

    inject('vform', Form)
}
