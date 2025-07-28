<template>
    <div class="employee-container">
        <ShiftAssignedList :assigned="assigned" />

        <br />
        <hr />
        <br />

        <ShiftAvailableList :unassigned="unassigned" @request="requestShift" />

        <br />
        <hr />
        <br />

        <ShiftRequestStatus
            :requests="requests"
            :activeStatus="activeStatus"
            @status-change="setStatus"
        />
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
import ShiftAvailableList from "./ShiftAvailableList.vue";
import ShiftAssignedList from "./ShiftAssignedList.vue";
import { formatShiftDateTime } from "../utils/formatShiftDateTime";

export default {
    name: "EmployeeInterface",
    components: {
        NotificationModal,
        ShiftRequestStatus,
        ShiftAvailableList,
        ShiftAssignedList,
    },
    data() {
        return {
            activeStatus: "all",
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
        this.fetchRequests();
    },
    methods: {
        formatShiftDateTime,
        setStatus(status) {
            this.activeStatus = status;
            this.fetchRequests();
        },
        fetchData() {
            const token = localStorage.getItem("token");
            const headers = { Authorization: `Bearer ${token}` };

            fetch(`${API_BASE}/shifts/assigned`, { headers })
                .then((res) => res.json())
                .then((data) => (this.assigned = data));

            fetch(`${API_BASE}/shifts/unassigned`, { headers })
                .then((res) => res.json())
                .then((data) => (this.unassigned = data));
        },
        fetchRequests() {
            const token = localStorage.getItem("token");
            const headers = { Authorization: `Bearer ${token}` };

            const statusParam =
                this.activeStatus && this.activeStatus !== "all"
                    ? `?status=${this.activeStatus}`
                    : "";

            fetch(`${API_BASE}/shifts/request/status${statusParam}`, {
                headers,
            })
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
                        message: "Shift has reqested successfully!.",
                        type: "success",
                    };
                    this.fetchData();
                })
                .catch((err) => {
                    this.notification = {
                        message: err?.error || "shift requested failed.",
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
