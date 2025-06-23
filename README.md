# Estudio La Ultima Casa - Full Stack Project

## Overview

This project provides a complete digital platform for artist collectives to showcase their work, combining a Laravel backend with Vue.js frontends for both public visitors and administrators. The application is containerized using Docker for easy development and deployment.

## Features

### Backend (Laravel)
- RESTful API endpoints for all content types
- Authentication with Sanctum (email/password + Google OAuth)
- Role-based permissions (admin, artist)
- Multilingual content support
- File upload handling
- PostgreSQL database

### Frontend (Vue.js)
- **Visitor Interface**:
  - Artist profiles and galleries
  - Workshop listings
  - News section
  - Responsive custom design
  - English/Spanish language support

- **Admin Dashboard**:
  - CRUD operations for all content types
  - PrimeVue component library
  - Form validation and file uploads
  - Toast notifications

## Technology Stack

### Backend
- PHP 8.2
- Laravel 10
- PostgreSQL
- Sanctum for API authentication

### Frontend
- Vue 3 (Composition API)
- Pinia for state management
- PrimeVue (admin interface)
- Tailwind CSS
- i18n for internationalization

### Infrastructure
- Docker
- Nginx
- PostgreSQL
- Adminer (database management)

## Project Structure

```
.
├── backend/              # Laravel application
├── frontend/             # Vue.js application
│   ├── admin/            # Admin dashboard
│   └── visitors/         # Public website
├── docker/               # Docker configuration
│   ├── nginx/            # Nginx config
│   ├── node/             # Node Dockerfile
│   └── php/              # PHP Dockerfile
└── docker-compose.yml    # Docker compose configuration
```

## Prerequisites

- Docker
- Docker Compose
- Node.js (for local frontend development)
- Composer (for local backend development)

## Installation

1. Clone the repository:
   ```bash
   git clone [repository-url]
   cd proyecto-la-ultima-casa
   ```

2. Create and configure environment files:
   ```bash
   cp backend/.env.example backend/.env
   cp frontend/.env.example frontend/.env
   ```

3. Build and start the containers:
   ```bash
   docker-compose up -d --build
   ```

4. Install backend dependencies and generate key:
   ```bash
   docker-compose exec backend composer install
   docker-compose exec backend php artisan key:generate
   ```

5. Run database migrations:
   ```bash
   docker-compose exec backend php artisan migrate
   ```

6. (Optional) Seed the database:
   ```bash
   docker-compose exec backend php artisan db:seed
   ```

7. Install frontend dependencies:
   ```bash
   docker-compose exec frontend npm install
   ```

8. Build the frontend:
   ```bash
   docker-compose exec frontend npm run build
   ```

## Running the Application

After installation, the application will be available at:

- **Public website**: http://localhost
- **Admin dashboard**: http://localhost:8080
- **API**: http://localhost/api
- **Database management**: http://localhost:8081 (Adminer)

## Development Workflow

### Backend Development
1. Make changes to files in `backend/`
2. The changes will be automatically reflected in the container

### Frontend Development
For live development with hot-reload:
```bash
docker-compose exec frontend npm run dev
```
Then access the frontend at http://localhost:5173