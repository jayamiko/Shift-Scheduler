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
import { API_BASE } from "../config";

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
        async login() {
            try {
                const response = await fetch(`${API_BASE}/login`, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        email: this.email,
                        password: this.password,
                    }),
                });

                const data = await response.json();

                if (!response.ok) {
                    if (
                        response.status === 401 ||
                        data.message === "Invalid credentials"
                    ) {
                        this.error = "Email atau password salah";
                    } else {
                        this.error = data.message || "Login gagal";
                    }
                    return;
                }

                if (data.token) {
                    localStorage.setItem("token", data.token);

                    const userRes = await fetch(`${API_BASE}/me`, {
                        headers: { Authorization: `Bearer ${data.token}` },
                    });

                    const user = await userRes.json();
                    this.$emit("login-success", user);
                } else {
                    this.error = "Login gagal";
                }
            } catch (err) {
                this.error = "Terjadi kesalahan saat login";
            }
        },
    },
};
</script>
