/* @vue/component */
export default {
    props: {
        readonly: {
            type: Boolean,
            required: false,
            default: () => false,
        },
    },

    data: () => ({
        formHasFiles: false,
    }),

    computed: {
        method () {
            return !this.formHasFiles && this[this.formInitialValuesProp] ? 'put' : 'post'
        },
    },

    mounted () {
        if (this.formInitialValuesProp) {
            this.$watch(this.formInitialValuesProp, this.onformInitialValuesPropChange, {
                immediate: true
            })
        }
    },

    methods: {
        onFormSuccess (data) {
            this.clearForm()
        },
        onFormCancelled () {
            this.clearForm()
        },
        onformInitialValuesPropChange () {
            this.fillForm()
        },
        clearForm () {
            this.form.clear()
            this.form.reset()
        },
        fillForm () {
            this.clearForm()
            this.form.fill(this.getFormValues())
        },
        getFormValues () {
            return { ...this[this.formInitialValuesProp] }
        },
    }
}
