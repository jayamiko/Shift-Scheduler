<template>
    <div>
        <h2>Shift Request</h2>
        <div class="status-tabs">
            <button
                v-for="status in statuses"
                :key="status"
                :class="{ active: activeStatus === status }"
                @click="$emit('status-change', status)"
            >
                {{ status }}
            </button>
        </div>

        <ul class="request-list">
            <li v-for="req in requests" :key="req.id" class="request-item">
                <div>
                    <strong>{{ req.user.name }}</strong>
                    <span class="request-status-text">
                        <span v-if="req.status === 'pending'">request to</span>
                        <span v-if="req.status === 'approved'"
                            >assigned to</span
                        >
                        <span v-if="req.status === 'rejected'"
                            >request rejected to</span
                        >
                        Shift #{{ req.shift_id }}
                    </span>
                </div>
                <div v-if="req.status === 'pending'" class="action-buttons">
                    <button class="approve" @click="$emit('approve', req.id)">
                        Approved
                    </button>
                    <button class="reject" @click="$emit('reject', req.id)">
                        Rejected
                    </button>
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    name: "ShiftRequestList",
    props: {
        requests: Array,
        activeStatus: String,
        statuses: {
            type: Array,
            default: () => ["pending", "approved", "rejected"],
        },
    },
};
</script>

<style scoped>
.status-tabs {
    margin-bottom: 1rem;
}

.status-tabs button {
    margin-right: 0.5rem;
    padding: 0.5rem 1rem;
    border: 1px solid #ccc;
    color: #333;
    background: #f9f9f9;
    cursor: pointer;
    border-radius: 4px;
    text-transform: capitalize;
}

.status-tabs button.active {
    background-color: #2d89ef;
    color: white;
    border-color: #2d89ef;
}

.request-status-text {
    margin-left: 4px;
}

.request-list {
    list-style: none;
    padding: 0;
}

.request-item {
    margin-bottom: 0.75rem;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

button.approve {
    background-color: #4caf50;
    color: white;
    border: none;
    padding: 0.4rem 0.75rem;
    border-radius: 4px;
    cursor: pointer;
}

button.reject {
    background-color: #f44336;
    color: white;
    border: none;
    padding: 0.4rem 0.75rem;
    border-radius: 4px;
    cursor: pointer;
    margin-left: 4px;
}
</style>
