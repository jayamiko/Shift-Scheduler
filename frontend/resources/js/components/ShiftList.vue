<template>
    <div>
        <h2>Shift Management</h2>
        <table class="shift-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Role</th>
                    <th>Location</th>
                    <th>Date & Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="shift in shifts" :key="shift.id">
                    <td>{{ shift.id }}</td>
                    <td>{{ shift.role.name }}</td>
                    <td>{{ shift.location }}</td>
                    <td>
                        {{
                            formatShiftDateTime(
                                shift.date,
                                shift.start_time,
                                shift.end_time
                            )
                        }}
                    </td>
                    <td>
                        <button @click="$emit('edit', shift.id)">Edit</button>
                        <button
                            class="danger"
                            @click="$emit('delete', shift.id)"
                        >
                            Delete
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
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
.shift-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
    font-family: Arial, sans-serif;
}

.shift-table th,
.shift-table td {
    border: 1px solid #ddd;
    padding: 0.75rem;
    text-align: left;
}

.shift-table th {
    background-color: #f2f2f2;
    text-transform: uppercase;
    font-size: 0.875rem;
    color: #333;
}

.shift-table tr:nth-child(even) {
    background-color: #fafafa;
}

button {
    padding: 0.4rem 0.75rem;
    border: none;
    border-radius: 4px;
    background-color: #2d89ef;
    color: white;
    cursor: pointer;
    margin-right: 0.5rem;
}

button:hover {
    background-color: #1c6cd1;
}

button.danger {
    background-color: #f44336;
}

button.danger:hover {
    background-color: #d32f2f;
}
</style>
