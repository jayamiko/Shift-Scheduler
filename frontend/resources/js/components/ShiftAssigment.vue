<template>
    <div class="shift-assignments">
        <h2>Shift Assignments</h2>

        <div class="assignment-filter">
            <input type="date" v-model="localFilter.date" />
            <input
                type="text"
                v-model="localFilter.user"
                placeholder="Employee Name"
            />
            <button @click="$emit('search', localFilter)">Search</button>
        </div>

        <table class="assignment-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Role</th>
                    <th>Location</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Assigned To</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="assign in assignments" :key="assign.id">
                    <td>{{ assign.id }}</td>
                    <td>{{ assign.role?.name || "Unknown Role" }}</td>
                    <td>{{ assign.location }}</td>
                    <td>{{ assign.date }}</td>
                    <td>
                        {{ assign.start_time.substring(0, 5) }} -
                        {{ assign.end_time.substring(0, 5) }}
                    </td>
                    <td>
                        <span v-if="assign.user">{{ assign.user.name }}</span>
                        <button v-else @click="$emit('assign', assign)">
                            Assign
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    name: "ShiftAssignments",
    props: {
        assignments: {
            type: Array,
            required: true,
        },
        filter: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            localFilter: { ...this.filter },
        };
    },
    watch: {
        filter: {
            handler(newVal) {
                this.localFilter = { ...newVal };
            },
            deep: true,
        },
    },
};
</script>

<style scoped>
.assignment-filter {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    align-items: center;
}

.assignment-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

.assignment-table th,
.assignment-table td {
    border: 1px solid #ccc;
    padding: 0.5rem;
    text-align: left;
}

.assignment-table th {
    background-color: #f2f2f2;
}
</style>
