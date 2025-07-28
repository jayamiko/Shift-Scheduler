<template>
    <div>
        <h2>Shift Management</h2>
        <ul class="shift-list">
            <li v-for="shift in shifts" :key="shift.id" class="shift-item">
                <div>
                    <p class="shiftId">SHIFT ID: {{ shift.id }}</p>
                    <h2>{{ shift.role.name }}</h2>
                    <i>at {{ shift.location }}</i>
                    <p>
                        {{
                            formatShiftDateTime(
                                shift.date,
                                shift.start_time,
                                shift.end_time
                            )
                        }}
                    </p>
                </div>
                <div class="action-buttons">
                    <button @click="$emit('edit', shift.id)">Edit</button>
                    <button class="danger" @click="$emit('delete', shift.id)">
                        Delete
                    </button>
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
import { formatShiftDateTime } from "../utils/formatShiftDateTime";

export default {
    name: "ShiftList",
    props: {
        shifts: {
            type: Array,
            required: true,
        },
        roles: {
            type: Array,
            required: true,
        },
    },
    methods: {
        formatShiftDateTime,
    },
};
</script>

<style scoped>
.shift-list {
    list-style: none;
    padding: 0;
}

.shift-item {
    margin-bottom: 0.75rem;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.shiftId {
    font-size: 12px;
}

.action-buttons button {
    margin-left: 0.5rem;
}

button.danger {
    background-color: #ff5722;
    color: white;
}
</style>
