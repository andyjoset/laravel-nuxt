export default defineNuxtPlugin(async (nuxtApp) => {
    await nuxtApp.$store.nuxtClientInit(nuxtApp)
})
