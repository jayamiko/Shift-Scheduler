<template>
    <form
        @submit.prevent="login"
        style="
            max-width: 400px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        "
    >
        <h2 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem">
            Login
        </h2>
        <div style="margin-bottom: 1rem">
            <label>Email</label><br />
            <input
                v-model="email"
                type="email"
                required
                style="
                    width: 100%;
                    border: 1px solid #ccc;
                    padding: 0.5rem;
                    border-radius: 4px;
                "
            />
        </div>
        <div style="margin-bottom: 1rem">
            <label>Password</label><br />
            <input
                v-model="password"
                type="password"
                required
                style="
                    width: 100%;
                    border: 1px solid #ccc;
                    padding: 0.5rem;
                    border-radius: 4px;
                "
            />
        </div>
        <button
            type="submit"
            style="
                background-color: #007bff;
                color: white;
                padding: 0.5rem 1rem;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            "
        >
            Login
        </button>
        <p v-if="error" style="color: red; margin-top: 0.5rem">{{ error }}</p>
    </form>
</template>

<script>
const API_BASE = "http://localhost:8000/api";

export default {
    name: "LoginForm",
    data() {
        return {
            email: "",
            password: "",
            error: null,
        };
    },
    methods: {
        login() {
            fetch(`${API_BASE}/login`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    email: this.email,
                    password: this.password,
                }),
            })
                .then((res) => res.json())
                .then((data) => {
                    if (data.token) {
                        localStorage.setItem("token", data.token);
                        fetch(`${API_BASE}/me`, {
                            headers: { Authorization: `Bearer ${data.token}` },
                        })
                            .then((res) => res.json())
                            .then((user) => this.$emit("login-success", user));
                    } else {
                        this.error = data.message || "Login gagal";
                    }
                })
                .catch(() => (this.error = "Login gagal"));
        },
    },
};
</script>
