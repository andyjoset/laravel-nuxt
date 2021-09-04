export default ({ store, redirect }) => {
    if (store.getters['auth/check']?.email_verified_at === null) {
        return redirect({ name: 'dashboard' })
    }
}
