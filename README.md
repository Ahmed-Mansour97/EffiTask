# EffiTask

## 📖 Overview
This project is a **RESTful API for a Task Management System**.  
The goal is to create a robust and scalable API that adheres to industry best practices.

---

## 🚀 Main Business Requirements

### Endpoints
- **Authentication**
  - For already seeded system actors.
- **Tasks**
  - Create a new task.
  - Retrieve a list of all tasks.
    - Allow filtering by:
      - Status
      - Due date range
      - Assigned user
  - Add task dependencies with other tasks.
    - A task cannot be completed until all dependencies are completed.
  - Retrieve details of a specific task including its dependencies.
  - Update the details of a task:
    - Title, description, assignee, due date
    - Status (`pending`, `in_progress`, `completed`, `canceled`)

### Endpoint Authorizations
- **Managers**
  - Can create/update a task.
  - Can assign tasks to a user.
- **Users**
  - Can retrieve only tasks assigned to them.
  - Can update only the status of tasks assigned to them.

---

## ⚙️ Main Technical Requirements
- Design system endpoints following **RESTful standards**.
- Implement:
  - Data validations
  - Stateless authentication (JWT)
  - Error handling
  - Database migrations & seeders
- Containerization (Docker) is a **plus**.

---

## 🛠️ Installation Guide

1. Clone the repository:  
   `git clone https://github.com/Ahmed-Mansour97/EffiTask.git`  
   `cd task-management-api`

2. Install dependencies:  
   `composer install`

3. Copy `.env.example` to `.env` and configure database credentials:  
   `cp .env.example .env`

4. Generate application key:  
   `php artisan key:generate`

5. Run migrations and seeders:  
   `php artisan migrate --seed`

   This will create two default users:

   - **Manager**  
     - Email: `manager@example.com`  
     - Password: `password`  
     - Role: `manager`

   - **User**  
     - Email: `user@example.com`  
     - Password: `password`  
     - Role: `user`

6. Start the development server:  
   `php artisan serve`

---

## 📖 API Documentation (Swagger)
Interactive API documentation is available at:

👉 [http://127.0.0.1:8000/api/documentation#/](http://127.0.0.1:8000/api/documentation#/)

Swagger UI allows you to:
- Explore all available endpoints
- Authorize with JWT token
- Test requests directly from the browser

---
