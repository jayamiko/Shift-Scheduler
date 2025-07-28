<!-- src/App.vue -->
<template>
  <div class="min-h-screen bg-blue-900">
    <div class="p-4 shadow bg-white flex justify-between">
      <h1 class="text-xl font-bold text-red-500">Shift Scheduler</h1>
      <button v-if="user" @click="logout">Logout</button>
    </div>

    <div v-if="!user" class="p-4">
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
}
</style>
