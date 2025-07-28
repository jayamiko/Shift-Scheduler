<template>
    <div class="admin-container">
        <h2>Manajemen Shift</h2>
        <ul class="shift-list">
            <li v-for="shift in shifts" :key="shift.id" class="shift-item">
                <span
                    >{{ shift.date }} - {{ shift.role }} @
                    {{ shift.location }}</span
                >
                <button class="danger" @click="deleteShift(shift.id)">
                    Hapus
                </button>
            </li>
        </ul>

        <form @submit.prevent="createShift" class="shift-form">
            <input
                v-model="newShift.date"
                placeholder="Tanggal (YYYY-MM-DD)"
                required
            />
            <input v-model="newShift.role" placeholder="Peran" required />
            <input v-model="newShift.location" placeholder="Lokasi" required />
            <button type="submit">Tambah Shift</button>
        </form>

        <h2>Permintaan Shift</h2>
        <ul class="request-list">
            <li v-for="req in requests" :key="req.id" class="request-item">
                <span>
                    User #{{ req.user_id }} minta Shift #{{ req.shift_id }} -
                    <strong>{{ req.status }}</strong>
                </span>
                <div class="action-buttons">
                    <button class="approve" @click="approve(req.id)">
                        Setujui
                    </button>
                    <button class="reject" @click="reject(req.id)">
                        Tolak
                    </button>
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
const API_BASE = "http://localhost:8000/api";

export default {
    name: "AdminInterface",
    data() {
        return {
            shifts: [],
            newShift: { date: "", role: "", location: "" },
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

            fetch(`${API_BASE}/shifts`, { headers })
                .then((res) => res.json())
                .then((data) => (this.shifts = data));

            fetch(`${API_BASE}/shifts/request/status`, { headers })
                .then((res) => res.json())
                .then((data) => (this.requests = data));
        },
        createShift() {
            const token = localStorage.getItem("token");
            fetch(`${API_BASE}/shift`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${token}`,
                },
                body: JSON.stringify(this.newShift),
            })
                .then((res) => res.json())
                .then(() => {
                    this.newShift = { date: "", role: "", location: "" };
                    this.fetchData();
                });
        },
        deleteShift(id) {
            const token = localStorage.getItem("token");
            fetch(`${API_BASE}/shifts/${id}`, {
                method: "DELETE",
                headers: { Authorization: `Bearer ${token}` },
            }).then(() => this.fetchData());
        },
        approve(id) {
            const token = localStorage.getItem("token");
            fetch(`${API_BASE}/admin/approve`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${token}`,
                },
                body: JSON.stringify({ request_id: id }),
            }).then(() => this.fetchData());
        },
        reject(id) {
            const token = localStorage.getItem("token");
            fetch(`${API_BASE}/admin/reject`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${token}`,
                },
                body: JSON.stringify({ request_id: id }),
            }).then(() => this.fetchData());
        },
    },
};
</script>

<style>
.admin-container {
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
}

.shift-item,
.request-item {
    margin-bottom: 0.75rem;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.shift-form {
    margin-top: 2rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    max-width: 400px;
}

.shift-form input {
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.shift-form button {
    padding: 0.5rem;
    background-color: #2d89ef;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

button {
    padding: 0.4rem 0.75rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

button.approve {
    background-color: #4caf50;
    color: white;
}

button.reject {
    background-color: #f44336;
    color: white;
}

button.danger {
    background-color: #ff5722;
    color: white;
}
</style>
