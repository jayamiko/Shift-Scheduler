<template>
    <div>
        <h2>Status Requested</h2>
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
                        <span v-if="req.status === 'pending'">requested</span>
                        <span v-else-if="req.status === 'approved'"
                            >assigned</span
                        >
                        <span v-else-if="req.status === 'rejected'"
                            >rejected</span
                        >
                        to Shift #{{ req.shift_id }}
                    </span>
                </div>
                <strong :class="statusClass(req.status)">
                    {{ req.status }}
                </strong>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    name: "ShiftRequestStatus",
    props: {
        requests: Array,
        activeStatus: String,
        statuses: {
            type: Array,
            default: () => ["all", "pending", "approved", "rejected"],
        },
    },
    methods: {
        statusClass(status) {
            switch (status.toLowerCase()) {
                case "approved":
                    return "status-approved";
                case "rejected":
                    return "status-rejected";
                case "pending":
                default:
                    return "status-pending";
            }
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

.status-approved {
    color: #2e7d32;
}

.status-rejected {
    color: #c62828;
}

.status-pending {
    color: #f9a825;
}
</style>
