<template>
    <section>
        <h2 class="title">Shift Available</h2>
        <table class="shift-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Role</th>
                    <th>Location</th>
                    <th>Date & Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="shift in unassigned" :key="shift.id">
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
                        <button @click="$emit('request', shift.id)">
                            Request
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </section>
</template>

<script>
import { formatShiftDateTime } from "../utils/formatShiftDateTime";

export default {
    name: "ShiftAvailableList",
    props: {
        unassigned: {
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
}

.shift-table th,
.shift-table td {
    border: 1px solid #ddd;
    padding: 0.75rem;
    text-align: left;
}

.shift-table th {
    background-color: #f2f2f2;
    font-weight: bold;
}

button {
    padding: 0.4rem 0.75rem;
    border: none;
    border-radius: 4px;
    background-color: #2d89ef;
    color: white;
    cursor: pointer;
}

button:hover {
    background-color: #1c6cd1;
}
</style>
