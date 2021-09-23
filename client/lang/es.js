import vuetify from 'vuetify/es5/locale/es'

export default {
    home: 'Home',
    about: 'Nosotros',
    contact: 'Contacto',
    login: 'Login',
    logout: 'Cerrar Sesión',
    sign_in: 'Ingresar',
    register: 'Registarse',
    no_account: {
        text: '¿No tienes una cuenta? {action}',
        action_text: 'Crear cuenta',
    },

    reset_password: 'Resetear Contraseña',
    forgot_password: '¿Olvidaste tu contraseña?',
    change_password: 'Actualizar contraseña',
    request_password_reset: 'Solicitar reseteo de contraseña',
    password_updated: '¡Tu contraseña ha sido actualizada!',

    avatar_updated: '¡Avatar actualizado correctamente!',
    avatar_deleted: 'Tu avatar fue eliminado.',

    verify_email: 'Verificar Email',
    email_verified: '¡Tu email ha sido verificado!',
    email_already_verified: '¡Tu email ya se encuentra verificado!',
    verification_link_sent: '¡Te hemos enviado por correo tu link de verificación!',
    invalid_email_verification_link: 'El link de verificación es inválido.',
    verification_link_recently_sent: '¡Te acabamos de enviar un link de verificación, utilizalo o intenta solicitar uno nuevo más tarde!',
    email_verification_required: {
        text: 'Verificación de email requerida, si aún no has recibido un link, haz click {action} para solicitar uno.',
        action_text: 'aquí',
    },

    avatar: 'Avatar',
    users: 'Usuario | Usuarios',
    user_info: 'Información del Usuario',
    roles: 'Rol | Roles',
    role_info: 'Información del Rol',
    permissions: 'Permiso | Permisos',
    roles_and_permissions: 'Roles & Permisos',

    search: 'Buscar',
    create: 'Crear {0}',
    edit: 'Editar {0}',
    update: 'Actualizar {0}',
    upload: 'Subir {0}',
    change: 'Cambiar {0}',
    delete: 'Eliminar {0}',
    id: 'ID',
    name: 'Nombre | Nombres',
    email: 'Email',
    status: 'Estado',
    actions: 'Acciones',
    loading: 'Cargando',
    reset: 'Resetear',
    active: 'Activo',
    banned: 'Bloqueado',

    dashboard: {
        title: 'Dashboard',
        welcome: '¡Bienvenido de vuelta {0}!',
    },

    profile: {
        me: 'Mi Perfil',
        title: 'Perfil',
    },

    alerts: {
        done: '¡Hecho!',
        sure: '¿Estás seguro?',
        created: '¡Creado correctamente!',
        updated: '¡Actualizado correctamente!',
        deleted: '¡Eliminado correctamente!',
        unauthorized: '¡No autorizado!',
        question: '¿Desea continuar?',
        will_delete: '¡No podrás deshacer esto después!',
        unban_user: 'El usuario podrá ingresar al sistema después de esto.',
        ban_user: 'El usuario no podrá ingresar al sistema hasta que su cuenta sea activada de nuevo.',
    },

    btns: {
        ok: 'Ok',
        save: 'Guardar',
        close: 'Cerrar',
        submit: 'Enviar',
        cancel: 'Cancelar',
        show: 'Ver',
        edit: 'Editar',
        delete: 'Eliminar',
        toggle_user_account: ({ named }) => `${named('active') ? 'Bloquear' : 'Activar'} cuenta de usuario`,
    },

    errors: {
        401: '¡No autenticado!',
        403: '¡Acción no autorizada!',
        404: '404 No Encontrado',
        500: 'Algo salió mal',
    },

    labels: {
        name: 'Nombre | Nombres',
        email: 'Email',
        role: 'Rol',
        avatar: 'Avatar',
        select: 'Seleccionar',
        password: 'Contraseña',
        remember: 'Recordarme',
        new_password: 'Nueva contraseña',
        current_password: 'Contraseña Actual',
        password_confirmation: 'Confirmar contraseña',
    },

    // Vuetify overrides
    $vuetify: {
        ...vuetify,
        dataFooter: {
            pageText: 'Mostrando {0}-{1} de {2} resultados',
        },
    },
}
