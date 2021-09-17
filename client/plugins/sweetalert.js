import SweetAlert from 'sweetalert2'

export default function ({ app, $i18n, $axios, $vform }, inject) {
    const swal = {}

    const Swal = SweetAlert.mixin({
        confirmButtonText: app.i18n.t('btns.ok'),
        cancelButtonText: app.i18n.t('btns.cancel'),
        willOpen: (container) => {
            const { currentTheme: colors, isDark } = app.vuetify.framework.theme
            const options = {
                confirmButtonColor: colors.primary,
                cancelButtonColor: colors.error,
                denyButtonColor: colors.error,
            }

            if (isDark) {
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
        title = app.i18n.t('alerts.question'),
        success = app.i18n.t('alerts.done'),
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
                            return form instanceof $vform
                                ? await form[method](url)
                                : await $axios[method](url, form ?? {})
                        } catch (e) {
                            Swal.showValidationMessage(
                                form instanceof $vform
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
            title: app.i18n.t('alerts.sure'),
            text: app.i18n.t('alerts.will_delete'),
            method: 'delete',
            ...options
        })
    }

    inject('swal', swal)
}
