<template>
    <section>
        <h2>Status Requested</h2>
        <ul class="request-list">
            <li
                v-for="req in requests"
                :key="req.id"
                class="request-item"
                :class="statusClass(req.status)"
            >
                <div>
                    Shift #{{ req.id }} requested by
                    {{ req.user.name }}
                </div>
                <strong>{{ req.status }}</strong>
            </li>
        </ul>
    </section>
</template>

<script>
import { formatShiftDateTime } from "../utils/formatShiftDateTime";

export default {
    name: "ShiftRequestStatus",
    props: {
        requests: {
            type: Array,
            required: true,
        },
    },
    methods: {
        formatShiftDateTime,
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
.request-list {
    list-style: none;
    padding: 0;
    margin: 0 0 1rem;
}

.request-item {
    padding: 0.5rem;
    margin-bottom: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 500;
}

.status-approved {
    background-color: #e6f4ea;
    color: #2e7d32;
    border-left: 5px solid #2e7d32;
}

.status-rejected {
    background-color: #fdecea;
    color: #c62828;
    border-left: 5px solid #c62828;
}

.status-pending {
    background-color: #fff8e1;
    color: #f9a825;
    border-left: 5px solid #f9a825;
}
</style>
