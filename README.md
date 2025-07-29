# 🗓️ Daily Worker Roster Management System

A fullstack application to manage daily worker shift schedules using Laravel (Backend) and Vue 3 + Laravel Mix (Frontend).

---

## 📦 Project Overview

This project is composed of:

- **Admin**: can create/edit/delete shifts, and approve or reject shift requests.
- **Employee**: can view shifts, request to take a shift, and monitor request status.

### Services (via Docker Compose):

- `app` → Laravel 12 (backend)
- `frontend` → Vue 3 + Laravel Mix (client)
- `db` → PostgreSQL

---

## ⚙️ Setup Instructions

1. Start all services using Docker Compose:

   ```bash
   docker compose up -d
   ```

If the backend hasn't been migrated yet, run the following commands inside the app container:

docker exec -it app bash
php artisan migrate
php artisan migrate:fresh --seed

Access the services:

Backend API: http://localhost:8000/

Frontend: http://localhost:3000/

### 🧾 Assumptions

Laravel Sanctum is used for authentication.

All protected API routes require a valid Bearer Token in the Authorization header.

Database seeding will insert default users and sample data for testing.

## 🔐 Login Credentials

You can use the seeded users to login:

### Admin User

| Email            | Password   |
| ---------------- | ---------- |
| `admin@mail.com` | `admin123` |

### Non-Admin Users

| Email            | Password  |
| ---------------- | --------- |
| `user1@mail.com` | `user123` |
| `user2@mail.com` | `user123` |

To create a custom user manually, use this API:

POST http://localhost:8000/api/register

form-data fields:
name
email
password
password_confirmation

After registration, you can login with your credentials and access the app accordingly.

## 🔌 API Usage

### 🛡️ Authentication

| Method | Endpoint        | Description                   |
| ------ | --------------- | ----------------------------- |
| POST   | `/api/register` | Register a new user           |
| POST   | `/api/login`    | Login and get Bearer token    |
| GET    | `/api/me`       | Get the current user info     |
| POST   | `/api/logout`   | Logout the authenticated user |

### 🗓️ Shift Management (Admin Only)

| Method | Endpoint                  | Description            |
| ------ | ------------------------- | ---------------------- |
| GET    | `/api/shifts`             | Get all shifts         |
| POST   | `/api/shift`              | Create a new shift     |
| PUT    | `/api/shifts/edit/{id}`   | Edit an existing shift |
| DELETE | `/api/shifts/delete/{id}` | Delete a shift         |

### 📥 Shift Requests (Employee)

| Method | Endpoint                   | Description                         |
| ------ | -------------------------- | ----------------------------------- |
| POST   | `/api/shift-requests`      | Request to take a shift             |
| GET    | `/api/my-requests`         | View your own shift requests        |
| GET    | `/api/pending-requests`    | (Admin) View all pending requests   |
| PUT    | `/api/shift-requests/{id}` | (Admin) Approve or reject a request |

### 🖥️ User Interface Walkthrough

#### 🔐 Login Page

Input email and password.

Upon success, users are redirected to their respective dashboards based on their role.

#### 👑 Admin Interface

View all created shifts.

Create/edit/delete new shifts.

View pending requests from workers.

Approve or reject shift requests.

Assign or reassign workers.

Finalize the shift roster.

#### 👷 Employee Interface

View assigned and unassigned (available) shifts.

Request to take an available shift.

Check status of submitted requests (pending / approved / rejected).

### 🔁 System Flow

```
          ┌────────────┐
          │   Login    │
          └────┬───────┘
               │
       ┌───────▼────────┐
       │ Check is_admin │
       └───────┬────────┘
   ┌───────────┴─────────────┐
   │                         │
┌──▼───┐               ┌─────▼─────┐
│Admin │               │ Employee  │
└──┬───┘               └────┬──────┘
   │                         │
   │ Manage shifts           │ View & request shifts
   │ Approve requests        │ View request status
   └──────────────┬──────────┘
                  ▼
         Shifts assigned & updated
```

## ⚙️ Technical Notes

API_BASE is defined in .env and injected into Vue via Laravel Mix `(process.env.MIX_API_BASE).`

All date/time inputs are converted to UTC format on the frontend before being sent to the backend.

Make sure CORS headers and Sanctum setup are correctly configured in the Laravel backend.
