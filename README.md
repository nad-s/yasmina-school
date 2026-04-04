# Yasmina School Management System

A mini school management system built with Laravel 13 (REST API) and Vue 3 (SPA).

## Tech Stack

- **Backend:** Laravel 13, Laravel Sanctum, SQLite
- **Frontend:** Vue 3, Vue Router, Pinia, Axios

## Features

- JWT-style token authentication via Laravel Sanctum
- Three roles: Admin, Teacher, Student
- Admin: full CRUD on classrooms and students, assigns teachers to classrooms
- Teacher: views and manages only their own classrooms and students
- Student: views their own classroom, can update their profile
- RESTful API with proper HTTP methods and status codes
- Role-based access enforced on both frontend and backend

## Project Structure

yasmina-school/       → Laravel backend
├── app/Http/Controllers/
│   ├── AuthController.php
│   ├── ClassroomController.php
│   └── StudentController.php
├── app/Models/
│   ├── User.php
│   ├── Classroom.php
│   └── Student.php
├── routes/api.php
└── database/migrations/
frontend/             → Vue 3 SPA
├── src/
│   ├── api/axios.js
│   ├── stores/auth.js
│   ├── router/index.js
│   └── views/
│       ├── LoginView.vue
│       ├── RegisterView.vue
│       ├── DashboardView.vue
│       ├── ClassroomsView.vue
│       └── StudentsView.vue


## How to Run

### Backend
```bash
cd yasmina-school
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

API runs at `http://localhost:8000`

### Frontend
```bash
cd yasmina-school/frontend
npm install
npm run dev
```

Frontend runs at `http://localhost:5173`

### Default Test Accounts

Register as a student via the UI, or use tinker to create admin/teacher accounts:
```bash
php artisan tinker
\App\Models\User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => bcrypt('123456'), 'role' => 'admin']);
\App\Models\User::create(['name' => 'Teacher', 'email' => 'teacher@test.com', 'password' => bcrypt('123456'), 'role' => 'teacher']);
```

## API Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | /api/register | No | Register as student |
| POST | /api/login | No | Login |
| POST | /api/logout | Yes | Logout |
| GET | /api/me | Yes | Get current user |
| GET | /api/classrooms | Yes | List classrooms |
| POST | /api/classrooms | Admin | Create classroom |
| PUT | /api/classrooms/{id} | Admin/Teacher | Update classroom |
| DELETE | /api/classrooms/{id} | Admin | Delete classroom |
| GET | /api/students | Yes | List students |
| POST | /api/students | Admin | Create student |
| PUT | /api/students/{id} | Yes | Update student |
| DELETE | /api/students/{id} | Admin | Delete student |