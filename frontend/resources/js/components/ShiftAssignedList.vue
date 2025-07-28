<template>
    <section>
        <h2 class="title">Shift Assigned</h2>
        <table class="shift-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Role</th>
                    <th>Location</th>
                    <th>Date & Time</th>
                    <th>Assigned To</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="shift in assigned" :key="shift.id">
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
                    <td>{{ shift.user?.name || "Unknown" }}</td>
                </tr>
            </tbody>
        </table>
    </section>
</template>

<script>
import { formatShiftDateTime } from "../utils/formatShiftDateTime";

export default {
    name: "ShiftAssignedList",
    props: {
        assigned: {
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
.title {
    color: #0169f1;
    text-transform: uppercase;
    margin-bottom: 1rem;
}

.shift-table {
    width: 100%;
    border-collapse: collapse;
    font-family: Arial, sans-serif;
    font-size: 0.95rem;
}

.shift-table th,
.shift-table td {
    padding: 0.75rem;
    border: 1px solid #ccc;
    text-align: left;
}

.shift-table th {
    background-color: #f4f4f4;
}

.shift-table tr:nth-child(even) {
    background-color: #fafafa;
}
</style>
