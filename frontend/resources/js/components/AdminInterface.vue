<template>
    <div class="admin-container">
        <ShiftAssignments
            :assignments="assignments"
            :filter="assignmentFilter"
            @search="handleSearch"
            @assign="openAssignModal"
        />

        <br />
        <hr />
        <br />

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

        <ShiftList
            :shifts="shifts"
            :roles="roles"
            @edit="openEditModal"
            @delete="deleteShift"
        />

        <br />
        <hr />
        <br />

        <ShiftRequestList
            :requests="requests"
            :activeStatus="activeStatus"
            @status-change="setStatus"
            @approve="approve"
            @reject="reject"
        />

        <!-- Modal Assign -->
        <div v-if="modalAssignShift" class="modal-backdrop">
            <div class="modal">
                <h3>Assign Shift #{{ modalAssignShift.id }}</h3>
                <form @submit.prevent="submitAssignment">
                    <select v-model="selectedUserId" required>
                        <option value="" disabled>Select User</option>
                        <option
                            v-for="user in nonAdminUsers"
                            :key="user.id"
                            :value="user.id"
                        >
                            {{ user.name }}
                        </option>
                    </select>

                    <div class="modal-actions">
                        <button class="btn-created" type="submit">
                            Assign
                        </button>
                        <button @click="closeAssignModal" type="button">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Shift -->
        <div v-if="editingShift" class="modal-backdrop">
            <div class="modal">
                <h3>Edit Shift</h3>
                <form @submit.prevent="updateShift">
                    <div class="form-group">
                        <label>Shift Date</label>
                        <input
                            type="date"
                            v-model="editingShift.date"
                            required
                        />
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

        <!-- Notification Modal -->
        <NotificationModal
            :message="notification.message"
            :type="notification.type"
            @close="notification.message = ''"
        />
    </div>
</template>

<script>
import { API_BASE } from "../config";
import { formatShiftDateTime } from "../utils/formatShiftDateTime";
import ShiftAssignments from "./ShiftAssigment.vue";
import ShiftList from "./ShiftList.vue";
import ShiftRequestList from "./ShiftRequestList.vue";
import NotificationModal from "./NotificationModal.vue";

export default {
    name: "AdminInterface",
    components: {
        ShiftAssignments,
        ShiftList,
        ShiftRequestList,
        NotificationModal,
    },
    data() {
        return {
            modalAssignShift: null,
            selectedUserId: "",
            users: [],
            editingShift: null,
            activeStatus: "pending",
            assignments: [],
            assignmentFilter: {
                date: "",
                user: "",
            },
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
            notification: {
                message: "",
                type: "",
            },
        };
    },
    computed: {
        nonAdminUsers() {
            return this.users.filter((u) => u.is_admin !== true);
        },
    },
    mounted() {
        this.fetchData();
        this.fetchRequests();
        this.fetchAssignments();
        fetch(`${API_BASE}/roles`)
            .then((res) => res.json())
            .then((data) => (this.roles = data));
        fetch(`${API_BASE}/users`)
            .then((res) => res.json())
            .then((data) => (this.users = data));
    },
    methods: {
        handleSearch(newFilter) {
            this.assignmentFilter = { ...newFilter };
            this.fetchAssignments();
        },
        openAssignModal(shift) {
            this.modalAssignShift = shift;
            this.selectedUserId = "";
        },
        closeAssignModal() {
            this.modalAssignShift = null;
            this.selectedUserId = "";
        },
        submitAssignment() {
            const token = localStorage.getItem("token");

            fetch(`${API_BASE}/admin/assign`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${token}`,
                },
                body: JSON.stringify({
                    shift_id: this.modalAssignShift.id,
                    user_id: this.selectedUserId,
                }),
            })
                .then((res) => {
                    if (!res.ok)
                        return res.json().then((err) => Promise.reject(err));
                    return res.json();
                })
                .then(() => {
                    this.notification = {
                        message: "Shift successfully assigned!",
                        type: "success",
                    };
                    this.closeAssignModal();
                    this.fetchData();
                    this.fetchRequests();
                    this.fetchAssignments();
                })
                .catch((err) => {
                    this.notification = {
                        message: err.message || "Failed to assign shift!",
                        type: "error",
                    };
                });
        },
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
        fetchAssignments() {
            const token = localStorage.getItem("token");
            const headers = { Authorization: `Bearer ${token}` };

            const { date, user } = this.assignmentFilter;
            const params = new URLSearchParams();

            if (date) params.append("date", date);
            if (user) params.append("user", user);

            fetch(`${API_BASE}/admin/assignments?${params.toString()}`, {
                headers,
            })
                .then((res) => res.json())
                .then((data) => {
                    this.assignments = data;
                })
                .catch((err) => {
                    console.error("Failed to fetch assignments:", err);
                });
        },
        fetchRequests() {
            const token = localStorage.getItem("token");
            const headers = { Authorization: `Bearer ${token}` };

            fetch(
                `${API_BASE}/shifts/request/status?status=${this.activeStatus}`,
                { headers }
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
                .then(() => {
                    this.notification = {
                        message: "Shift created successfully!",
                        type: "success",
                    };
                    this.newShift = {
                        date: "",
                        start_time: "",
                        end_time: "",
                        role_id: "",
                        location: "",
                    };
                    this.fetchData();
                    this.fetchAssignments();
                })
                .catch((err) => {
                    console.error(err);
                    this.notification = {
                        message: err.message || "Shift creation failed.",
                        type: "error",
                    };
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
                    this.notification = {
                        message: "Shift update successfully.",
                        type: "success",
                    };

                    this.editingShift = null;
                    this.fetchRequests();
                    this.fetchData();
                    this.fetchAssignments();
                })
                .catch((err) => {
                    console.error("Update failed:", err.message);
                    this.notification = {
                        message: err.message || "Shift update failed.",
                        type: "error",
                    };
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
                this.fetchAssignments();
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
                this.fetchAssignments();
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

h2 {
    color: #333;
    margin: 0px;
    margin-bottom: 1rem;
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
    margin-left: 4px;
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
