import vuetify from 'vuetify/es5/locale/en'

export default {
    home: 'Home',
    about: 'About Us',
    contact: 'Contact',
    login: 'Login',
    logout: 'Logout',
    sign_in: 'Sign In',
    register: 'Register',
    no_account: {
        text: 'Don\'t have an account? {action}',
        action_text: 'Create account',
    },

    reset_password: 'Reset Password',
    forgot_password: 'Forgot password?',
    change_password: 'Change current password',
    request_password_reset: 'Request Password Reset',
    password_updated: 'Your password has been updated!',
    confirm_password: 'Confirm password',
    protected_area: 'This area is protected, please confirm your password to continue.',

    avatar_updated: 'Avatar updated successfully',
    avatar_deleted: 'Your avatar has been deleted.',

    verify_email: 'Verify Email',
    email_verified: 'Your email has been verified!',
    email_already_verified: 'Your email is already verified!',
    verification_link_sent: 'We have e-mailed your verification link!',
    invalid_email_verification_link: 'The verification link is invalid.',
    verification_link_recently_sent: 'We just sent you a verification link, please use it or try requesting another later!',
    email_verification_required: {
        text: 'Email verification required, if you didn\'t receive an email yet, click {action} to request one.',
        action_text: 'here',
    },

    avatar: 'Avatar',
    users: 'User | Users',
    user_info: 'User info',
    roles: 'Role | Roles',
    role_info: 'Role info',
    permissions: 'Permission | Permissions',
    roles_and_permissions: 'Roles & Permissions',

    search: 'Search',
    search_hint: 'Press enter for a deeper search',

    add: 'Add {0}',
    show: 'Show {0}',
    create: 'Create {0}',
    edit: 'Edit {0}',
    update: 'Update {0}',
    upload: 'Upload {0}',
    change: 'Change {0}',
    delete: 'Delete {0}',
    id: 'ID',
    name: 'Name | Names',
    email: 'Email',
    status: 'Status',
    actions: 'Actions',
    loading: 'Loading',
    reset: 'Reset',
    active: 'Active',
    banned: 'Banned',
    item: 'Item',
    filters: 'filters',
    filter_by: 'Filter By:',
    user_status: 'User Account Status',

    dashboard: {
        title: 'Dashboard',
        welcome: 'Welcome {0} you\'re logged in!',
    },

    profile: {
        me: 'My Profile',
        title: 'Profile',
    },

    alerts: {
        done: 'Done!',
        sure: 'Are you sure?',
        created: 'Created successfully!',
        updated: 'Updated successfully!',
        deleted: 'Deleted successfully!',
        unauthorized: 'Unauthorized!',
        question: 'Do you want to continue?',
        will_delete: 'You won\'t be able to undo this later!',
        unban_user: 'The user will be able to log into the application before this.',
        ban_user: 'The user won\'t be able to log into the application until the account is activated again.',
    },

    btns: {
        ok: 'Ok',
        save: 'Save',
        close: 'Close',
        submit: 'Submit',
        cancel: 'Cancel',
        show: 'Show',
        edit: 'Edit',
        delete: 'Delete',
        confirm: 'Confirm',
        toggle_user_account: ({ named }) => `${named('active') ? 'Ban' : 'Activate'} user account`,
    },

    errors: {
        401: 'Unauthorized!',
        403: 'Forbidden!',
        404: '404 Not Found',
        500: 'An error occurred',
    },

    labels: {
        all: 'All',
        name: 'Name | Names',
        email: 'Email',
        role: 'Role',
        avatar: 'Avatar',
        select: 'Select',
        password: 'Password',
        remember: 'Remember me',
        new_password: 'New password',
        enter_password: 'Enter Password',
        current_password: 'Current password',
        password_confirmation: 'Confirm password',
    },

    // Vuetify overrides
    $vuetify: {
        ...vuetify,
        dataFooter: {
            pageText: 'Showing {0}-{1} of {2} results',
        },
    },
}
