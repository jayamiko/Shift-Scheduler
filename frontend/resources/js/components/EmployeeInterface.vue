<template>
    <div class="employee-container">
        <section>
            <h2>Shift Saya</h2>
            <ul class="shift-list">
                <li
                    v-for="shift in assigned"
                    :key="shift.id"
                    class="shift-item"
                >
                    {{ shift.date }} - {{ shift.role }} @ {{ shift.location }}
                </li>
            </ul>
        </section>

        <section>
            <h2>Shift Tersedia</h2>
            <ul class="shift-list">
                <li
                    v-for="shift in unassigned"
                    :key="shift.id"
                    class="shift-item"
                >
                    <span
                        >{{ shift.date }} - {{ shift.role }} @
                        {{ shift.location }}</span
                    >
                    <button @click="requestShift(shift.id)">Ajukan</button>
                </li>
            </ul>
        </section>

        <section>
            <h2>Status Permintaan</h2>
            <ul class="request-list">
                <li v-for="req in requests" :key="req.id" class="request-item">
                    Shift #{{ req.shift_id }} -
                    <strong>{{ req.status }}</strong>
                </li>
            </ul>
        </section>
    </div>
</template>

<script>
const API_BASE = "http://localhost:8000/api";

export default {
    name: "EmployeeInterface",
    data() {
        return {
            assigned: [],
            unassigned: [],
            requests: [],
        };
    },
    mounted() {
        this.fetchData();
    },
    methods: {
        fetchData() {
            const token = localStorage.getItem("token");
            const headers = { Authorization: `Bearer ${token}` };

            fetch(`${API_BASE}/shifts/assigned`, { headers })
                .then((res) => res.json())
                .then((data) => (this.assigned = data));

            fetch(`${API_BASE}/shifts/unassigned`, { headers })
                .then((res) => res.json())
                .then((data) => (this.unassigned = data));

            fetch(`${API_BASE}/shifts/request/status`, { headers })
                .then((res) => res.json())
                .then((data) => (this.requests = data));
        },
        requestShift(shiftId) {
            const token = localStorage.getItem("token");
            fetch(`${API_BASE}/shifts/request`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${token}`,
                },
                body: JSON.stringify({ shift_id: shiftId }),
            })
                .then((res) => res.json())
                .then(() => this.fetchData());
        },
    },
};
</script>

<style>
.employee-container {
    padding: 1rem;
    font-family: Arial, sans-serif;
}

h2 {
    margin-top: 2rem;
    margin-bottom: 1rem;
    color: #333;
}

.shift-list,
.request-list {
    list-style: none;
    padding: 0;
    margin: 0 0 1rem;
}

.shift-item,
.request-item {
    padding: 0.5rem;
    margin-bottom: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    display: flex;
    justify-content: space-between;
    align-items: center;
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
