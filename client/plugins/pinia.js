import { useMainStore } from '~/store'

export default defineNuxtPlugin(({ $pinia, provide }) => {
    provide('store', useMainStore($pinia))
})
