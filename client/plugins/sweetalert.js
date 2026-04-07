import SweetAlert from 'sweetalert2'

export default defineNuxtPlugin((nuxtApp) => {
    const swal = {}

    const Swal = SweetAlert.mixin({
        confirmButtonText: nuxtApp.$i18n.t('btns.ok'),

        cancelButtonText: nuxtApp.$i18n.t('btns.cancel'),

        willOpen: (container) => {
            const { colors, dark } = nuxtApp.$vuetify.theme.current.value
            const options = {
                confirmButtonColor: colors.primary,
                cancelButtonColor: colors.error,
                denyButtonColor: colors.error,
            }

            if (dark) {
                options.background = '#1E1E1E'
                container.getElementsByClassName('swal2-title')[0].style.color = '#E0E0E0'
                container.getElementsByClassName('swal2-html-container')[0].style.color = '#E0E0E0'
            }

            Swal.update(options)
        },
    })

    swal.message = function (options) {
        return Swal.fire(options)
    }

    swal.info = function (options) {
        return this.message({ ...options, icon: 'info' })
    }

    swal.success = function (options) {
        return this.message({ ...options, icon: 'success' })
    }

    swal.question = function (options) {
        return this.message({ ...options, icon: 'question' })
    }

    swal.error = function (options) {
        return this.message({ ...options, icon: 'error' })
    }

    swal.warning = function (options) {
        return this.message({ ...options, icon: 'warning' })
    }

    swal.confirm = async function ({
        url,
        form,
        text,
        title = nuxtApp.$i18n.t('alerts.question'),
        success = nuxtApp.$i18n.t('alerts.done'),
        method = 'post',
        options = {}
    }) {
        try {
            const result = await Swal.fire({
                text,
                title,
                focusCancel: true,
                reverseButtons: true,
                showCancelButton: true,
                showLoaderOnConfirm: Boolean(url),
                backdrop: () => !Swal.isLoading(),
                allowOutsideClick: () => !Swal.isLoading(),
                preConfirm: url
                    ? async () => {
                        try {
                            return form instanceof nuxtApp.$vform
                                ? await form[method](url)
                                : await nuxtApp.$axios[method](url, form ?? {})
                        } catch (e) {
                            Swal.showValidationMessage(
                                form instanceof nuxtApp.$vform
                                    ? form.errors.first()
                                    : e?.response?.data?.message || e
                            )
                        }
                    }
                    : undefined,
                ...options,
            })

            if (result.isConfirmed) {
                if (success) {
                    this.success({ title: success })
                }

                return result.value
            }

            return result
        } catch (e) {
        }
    }

    swal.delete = function (options = {}) {
        return this.confirm({
            title: nuxtApp.$i18n.t('alerts.sure'),
            text: nuxtApp.$i18n.t('alerts.will_delete'),
            method: 'delete',
            ...options
        })
    }

    nuxtApp.provide('swal', swal)
})
