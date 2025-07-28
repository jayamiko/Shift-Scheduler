<!-- components/NotificationModal.vue -->
<template>
    <div v-if="message" class="modal-overlay">
        <div class="modal-content" :class="type">
            <p>{{ message }}</p>
            <button @click="$emit('close')">Close</button>
        </div>
    </div>
</template>

<script>
export default {
    name: "NotificationModal",
    props: {
        message: String,
        type: {
            type: String,
            default: "success", // success || error
        },
    },
};
</script>

<style scoped>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 999;
}

.modal-content {
    background-color: white;
    padding: 1.5rem;
    border-radius: 8px;
    text-align: center;
    max-width: 300px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
}

.modal-content.success {
    border-top: 5px solid #28a745;
}

.modal-content.error {
    border-top: 5px solid #dc3545;
}

.modal-content p {
    margin: 0 0 1rem;
    font-size: 1rem;
}

.modal-content button {
    padding: 0.5rem 1rem;
    background-color: #2d89ef;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
</style>
