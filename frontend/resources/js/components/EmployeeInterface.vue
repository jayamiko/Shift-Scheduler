<template>
    <div class="employee-container">
        <section>
            <h2 class="title">Shift Assigned</h2>
            <ul class="shift-list">
                <li
                    v-for="shift in assigned"
                    :key="shift.id"
                    class="shift-item"
                >
                    <div>
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
                </li>
            </ul>
        </section>

        <br />
        <hr />
        <br />

        <section>
            <h2 class="title">Shift Available</h2>
            <ul class="shift-list">
                <li
                    v-for="shift in unassigned"
                    :key="shift.id"
                    class="shift-item"
                >
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
                    <button @click="requestShift(shift.id)">Request</button>
                </li>
            </ul>
        </section>

        <br />
        <hr />
        <br />

        <ShiftRequestStatus :requests="requests" />
    </div>

    <NotificationModal
        :message="notification.message"
        :type="notification.type"
        @close="notification.message = ''"
    />
</template>

<script>
import { API_BASE } from "../config";
import NotificationModal from "./NotificationModal.vue";
import ShiftRequestStatus from "./ShiftRequestStatus.vue";
import { formatShiftDateTime } from "../utils/formatShiftDateTime";

export default {
    name: "EmployeeInterface",
    components: {
        NotificationModal,
        ShiftRequestStatus,
    },
    data() {
        return {
            assigned: [],
            unassigned: [],
            requests: [],
            notification: {
                message: "",
                type: "",
            },
        };
    },
    mounted() {
        this.fetchData();
    },
    methods: {
        formatShiftDateTime,
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
                .then(async (res) => {
                    const data = await res.json();
                    if (!res.ok) throw data;
                    this.notification = {
                        message: "Berhasil mengajukan shift.",
                        type: "success",
                    };
                    this.fetchData();
                })
                .catch((err) => {
                    this.notification = {
                        message: err?.error || "Gagal mengajukan shift.",
                        type: "error",
                    };
                });
        },
    },
};
</script>

<style>
.employee-container {
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
