export default function useForm (data, formInitialValues, formValues) {
    const { $vform } = useNuxtApp()

    const form = ref($vform.make(data))
    const formHasFiles = ref(false)
    const method = computed(() => {
        return !formHasFiles.value && formInitialValues ? 'put' : 'post'
    })

    onMounted(() => {
        if (formInitialValues) {
            watch(formInitialValues, onformInitialValuesPropChange, {
                immediate: true
            })
        }
    })

    function onFormSuccess (data) {
        clearForm()
    }

    function onFormCancelled () {
        clearForm()
    }

    function onformInitialValuesPropChange () {
        fillForm()
    }

    function clearForm () {
        form.value.clear()
        form.value.reset()
    }

    function fillForm () {
        clearForm()
        form.value.fill(getFormValues())
    }

    function getFormValues () {
        return formValues ? formValues() : { ...formInitialValues }
    }

    return {
        form: form.value,
        formHasFiles,
        method,
        onFormSuccess,
        onFormCancelled,
        onformInitialValuesPropChange,
        clearForm,
        fillForm,
        getFormValues,
    }
}
