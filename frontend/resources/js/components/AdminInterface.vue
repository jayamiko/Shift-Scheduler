<template>
    <div class="admin-container">
        <form @submit.prevent="createShift" class="shift-form">
            <div class="form-group">
                <label>Shift Date</label>
                <input type="date" v-model="newShift.date" required />
            </div>

            <div class="form-group">
                <label>Start Time</label>
                <input type="time" v-model="newShift.start_time" required />
            </div>

            <div class="form-group">
                <label>End Time</label>
                <input type="time" v-model="newShift.end_time" required />
            </div>

            <div class="form-group">
                <label>Role</label>
                <select v-model="newShift.role_id" required>
                    <option disabled value="">Select Role</option>
                    <option
                        v-for="role in roles"
                        :key="role.id"
                        :value="role.id"
                    >
                        {{ role.name }}
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label>Location</label>
                <input type="text" v-model="newShift.location" required />
            </div>

            <button class="btn-created" type="submit">Create New Shift</button>
        </form>

        <br />
        <hr />
        <br />

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
                    <button @click="openEditModal(shift.id)">Edit</button>
                    <button class="danger" @click="deleteShift(shift.id)">
                        Delete
                    </button>
                </div>
            </li>
        </ul>

        <br />
        <hr />
        <br />

        <h2>Shift Request</h2>
        <div class="status-tabs">
            <button
                v-for="status in ['pending', 'approved', 'rejected']"
                :key="status"
                :class="{ active: activeStatus === status }"
                @click="setStatus(status)"
            >
                {{ status }}
            </button>
        </div>
        <ul class="request-list">
            <li v-for="req in requests" :key="req.id" class="request-item">
                <div>
                    <strong>
                        {{ req.user.name }}
                    </strong>
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
                    <button class="approve" @click="approve(req.id)">
                        Approved
                    </button>
                    <button class="reject" @click="reject(req.id)">
                        Rejected
                    </button>
                </div>
            </li>
        </ul>
    </div>

    <div v-if="editingShift" class="modal-backdrop">
        <div class="modal">
            <h3>Edit Shift</h3>
            <form @submit.prevent="updateShift">
                <div class="form-group">
                    <label>Shift Date</label>
                    <input type="date" v-model="editingShift.date" required />
                </div>

                <div class="form-group">
                    <label>Start Time</label>
                    <input
                        type="time"
                        v-model="editingShift.start_time"
                        required
                    />
                </div>

                <div class="form-group">
                    <label>End Time</label>
                    <input
                        type="time"
                        v-model="editingShift.end_time"
                        required
                    />
                </div>

                <div class="form-group">
                    <label>Role</label>
                    <select v-model="editingShift.role_id" required>
                        <option
                            v-for="role in roles"
                            :key="role.id"
                            :value="role.id"
                        >
                            {{ role.name }}
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Location</label>
                    <input
                        type="text"
                        v-model="editingShift.location"
                        required
                    />
                </div>

                <div class="modal-actions">
                    <button class="btn-created" type="submit">
                        Save Changes
                    </button>
                    <button type="button" @click="editingShift = null">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { API_BASE } from "../config";
import { formatShiftDateTime } from "../utils/formatShiftDateTime";

export default {
    name: "AdminInterface",
    data() {
        return {
            editingShift: null,
            activeStatus: "pending",
            shifts: [],
            newShift: {
                date: "",
                start_time: "",
                end_time: "",
                role_id: "",
                location: "",
            },
            roles: [],
            requests: [],
        };
    },
    mounted() {
        this.fetchData();
        this.fetchRequests();
        fetch(`${API_BASE}/roles`)
            .then((res) => res.json())
            .then((data) => (this.roles = data));
    },
    methods: {
        openEditModal(id) {
            const token = localStorage.getItem("token");
            const headers = { Authorization: `Bearer ${token}` };

            fetch(`${API_BASE}/shifts/${id}`, { headers })
                .then((res) => res.json())
                .then((data) => {
                    this.editingShift = {
                        ...data,
                        start_time: data.start_time?.substring(0, 5),
                        end_time: data.end_time?.substring(0, 5),
                    };
                });
        },
        setStatus(status) {
            this.activeStatus = status;
            this.fetchRequests();
        },
        formatShiftDateTime,
        fetchData() {
            const token = localStorage.getItem("token");
            const headers = { Authorization: `Bearer ${token}` };

            fetch(`${API_BASE}/shifts`, { headers })
                .then((res) => res.json())
                .then((data) => (this.shifts = data));
        },
        fetchRequests() {
            const token = localStorage.getItem("token");
            const headers = { Authorization: `Bearer ${token}` };

            fetch(
                `${API_BASE}/shifts/request/status?status=${this.activeStatus}`,
                {
                    headers,
                }
            )
                .then((res) => res.json())
                .then((data) => (this.requests = data));
        },
        createShift() {
            const token = localStorage.getItem("token");

            const payload = {
                ...this.newShift,
                start_time: this.newShift.start_time?.substring(0, 5),
                end_time: this.newShift.end_time?.substring(0, 5),
            };

            fetch(`${API_BASE}/shift`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${token}`,
                },
                body: JSON.stringify(payload),
            })
                .then((res) => res.json())
                .then((data) => {
                    alert("Shift created successfully!");
                    this.newShift = {
                        date: "",
                        start_time: "",
                        end_time: "",
                        role_id: "",
                        location: "",
                    };
                    this.fetchData();
                })
                .catch((err) => {
                    console.error(err);
                    alert("Shift failed created.");
                });
        },
        updateShift() {
            const token = localStorage.getItem("token");

            const payload = {
                date: this.editingShift.date,
                start_time: this.editingShift.start_time,
                end_time: this.editingShift.end_time,
                location: this.editingShift.location,
                role_id: this.editingShift.role_id,
            };

            fetch(`${API_BASE}/shifts/edit/${this.editingShift.id}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${token}`,
                },
                body: JSON.stringify(payload),
            })
                .then((res) => {
                    if (!res.ok)
                        throw new Error(`HTTP error! status: ${res.status}`);
                    return res.json();
                })
                .then(() => {
                    this.editingShift = null;
                    this.fetchRequests();
                    this.fetchData();
                })
                .catch((err) => {
                    console.error("Update failed:", err.message);
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
            }).then(() => {
                this.fetchRequests();
                this.fetchData();
            });
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
            }).then(() => {
                this.fetchRequests();
                this.fetchData();
            });
        },
    },
};
</script>

<style>
.admin-container {
    padding: 1rem;
    font-family: Arial, sans-serif;
}

.title {
    color: #0169f1;
    text-transform: uppercase;
}

h2 {
    color: #333;
    margin: 0px;
    margin-bottom: 1rem;
}

.shiftId {
    font-size: 12px;
}

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
    display: flex;
    flex-direction: column;
    gap: 1rem;
    max-width: 400px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

label {
    font-weight: bold;
    margin-bottom: 0.25rem;
}

input,
select {
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.btn-created {
    padding: 0.6rem;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-created:hover {
    background-color: #218838;
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
    margin-left: 4px;
}

button.danger {
    background-color: #ff5722;
    color: white;
}

.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 999;
}

.modal {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    width: 400px;
    max-width: 90%;
}

.modal h3 {
    margin-top: 0;
    margin-bottom: 1rem;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
    margin-top: 1rem;
}

.modal-actions button {
    padding: 0.5rem 1rem;
}
</style>
