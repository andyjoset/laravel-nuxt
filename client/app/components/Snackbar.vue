<template>
    <v-snackbar
        v-for="(message, i) in messages"
        :key="`app_snackbar_message_${i}`"
        location="top right"
        :model-value="true"
        :color="message.color"
        :timeout="message.timeout"
        :variant="message.variant"
        :transition="i % 2 ? 'scroll-x-transition' : 'fab-transition'"
        :style="{
            'margin-top': `${i * 56}px`,
        }">
        {{ message.text }}
        <template #actions>
            <v-btn :icon="message.icon" @click="messages.splice(i, 1)" />
        </template>
    </v-snackbar>
</template>

<script setup>
    const { $store } = useNuxtApp()

    const messages = ref([])

    function addMessage (message) {
        const messageId = Math.random().toString()
        messages.value.push({
            id: messageId,
            color: message.color,
            text: message.message,
            timeout: message.timeout,
            variant: message.variant,
            icon: getIcon(message.color),
        })

        setTimeout(() => {
            const index = messages.value.findIndex(message => message.id === messageId)

            if (index !== -1) {
                messages.value.splice(index, 1)
            }
        }, message.timeout > 0 ? message.timeout : 1000000)
    }

    function getIcon (name) {
        const icons = [
            { name: 'info', icon: 'mdi-information' },
            { name: 'error', icon: 'mdi-close' },
            { name: 'success', icon: 'mdi-check' },
            { name: 'warning', icon: 'mdi-alert' },
        ]

        return icons.find(icon => icon.name === name)?.icon
    }

    onMounted (() => {
        $store.$onAction(({ name, after }) => {
            if (name === 'showSnackbarMessage') {
                after(() => addMessage($store.snackbar))
            }
        })
    })
</script>
