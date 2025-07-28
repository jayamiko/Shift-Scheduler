<template>
    <div class="container">
        <div class="navbar">
            <h1 class="title">Shift Schedul</h1>
            <button v-if="user" @click="logout">Logout</button>
        </div>

        <div v-if="!user" class="content">
            <!-- <LoginForm @login-success="handleLoginSuccess" /> -->
        </div>

        <div v-else>
            <!-- <AdminInterface v-if="user.is_admin" />
            <EmployeeInterface v-else /> -->
        </div>
    </div>
</template>

<script>
// import LoginForm from "./components/LoginForm.vue";
// import AdminInterface from "./components/AdminInterface.vue";
// import EmployeeInterface from "./components/EmployeeInterface.vue";

export default {
    name: "App",
    components: {
        // LoginForm,
        // AdminInterface,
        // EmployeeInterface,
    },
    data() {
        return {
            user: null,
        };
    },
    methods: {
        handleLoginSuccess(user) {
            this.user = user;
        },
        logout() {
            fetch("/api/logout", {
                method: "POST",
                headers: {
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                },
            }).then(() => {
                localStorage.removeItem("token");
                this.user = null;
            });
        },
    },
    mounted() {
        const token = localStorage.getItem("token");
        if (token) {
            fetch("/api/me", {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            })
                .then((res) => res.json())
                .then((data) => {
                    this.user = data;
                })
                .catch(() => localStorage.removeItem("token"));
        }
    },
};
</script>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
}

.container {
    min-height: 100vh;
}

.navbar {
    padding: 1rem;
    background-color: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.title {
    font-size: 1.25rem;
    font-weight: bold;
}

.content {
    padding: 1rem;
}
</style>
