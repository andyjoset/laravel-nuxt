export default async ({ $auth, redirect, route }) => {
    const { confirmed } = await $auth.confirmedPasswordStatus()

    if (!confirmed) {
        return redirect({ name: 'password.confirm', query: { redirect: route.fullPath } })
    }
}
