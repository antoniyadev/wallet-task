# 💸 Wallet Task

This is a full-stack Laravel + React wallet application developed as part of a technical assessment. It features user roles (admin/merchant), wallet transactions, top-up orders, and internal money transfers. The app is containerized using Docker and works seamlessly across macOS, Ubuntu, and Windows (via WSL2).

---

## 🚀 Setup Instructions

### 🔧 Requirements

- Docker with Docker Compose v2+
- Works on:
  - ✅ macOS (Intel or Apple Silicon)
  - ✅ Ubuntu
  - ✅ Windows (WSL2 recommended)

> This setup runs completely inside Docker. Your system does **not** need PHP, MySQL, or Laravel installed natively.

---

### 📦 Clone and Run

```bash
git clone https://github.com/antoniyadev/wallet-task.git
cd wallet-task
cp .env.example .env
docker compose up -d --build
```

---

### ⚙️ Initial Laravel Setup

After containers are running:

```bash
docker exec -it wallet-app bash

# Inside the container:
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
```

---

## 🌐 Access the App

- Laravel backend: [http://localhost:8080](http://localhost:8080)
- React frontend: [http://localhost:3000](http://localhost:3000)

---

## 👤 Test Users

| Role     | Email                  | Password          |
|----------|------------------------|-------------------|
| Admin    | admin@example.com      | secretpassword    |
| Merchant | merchant@example.com   | merchantpassword  |

---

## 🐘 Database Schema Overview

| Table         | Purpose                                                   |
|---------------|-----------------------------------------------------------|
| `users`       | Admins and merchants with roles, wallet amount            |
| `orders`      | Pending or completed top-up requests                      |
| `transactions`| Credits/debits tied to orders or manual/admin actions     |

---

## 🧑‍💻 Roles & Features

### 🛡️ Admin

- View all users
- Create credit/debit transactions
- Approve or refund user orders

### 🛒 Merchant

- View own transactions
- Create top-up orders (pending by default)
- Transfer money to other users

---

## 🧪 Testing (Optional)

```bash
docker exec -it wallet-app php artisan test
```

---

## 🧰 Docker Structure

| Component       | Description                         |
|------------------|-------------------------------------|
| `wallet-app`     | Laravel app in PHP 7.4 Apache image |
| `wallet-db`      | MySQL 5.7 with wallet schema        |

---

## 📂 Folder Layout

- `frontend/` — React application (SPA)
- `docker/` — Dockerfile for PHP environment
- `routes/` — Route definitions
- `app/Models/` — `User`, `Order`, `Transaction` models
- `app/Services/` — Business logic for order/transfer handling
- `resources/views/` — Blade templates
- `tests/` — Unit and feature tests

---

## ⚛️ React Frontend

The frontend is built with React and Bootstrap 4.

### Run frontend:

```bash
cd frontend
npm install
npm start
```

The app runs at [http://localhost:3000](http://localhost:3000) and interacts with Laravel through cookies and CSRF protection.

---

## ✍️ Author

Antoniya Stefanova — Full-Stack Developer  
This project was completed as part of a technical assessment.