
# Project Title

## Overview

### About the Application
This application could allow users to exchange message or chatting to each other. The technologies behind it include Laravel, JWT token, Redis, and MySQL.

### Key Features
- User authentication and authorization using JWT
- Simple messaging

---

## Architecture

### Application Architecture
The application is mainly using MVC architecture from Laravel without Views component. It is intended to provide only API interfaces without frontend web pages.

#### Components:
- **Framework**: Laravel 11
- **Database**: MySQL
- **Caching**: Redis
- **Authorization**: JWT token

### Diagram
The diagram will be shown here

---

## ERD (Entity Relationship Diagram)

### Database Design
There are only 2 entities in the database including User and Message. These entities are tied with one-to-many relationship from User to Message.

### ERD Diagram
Insert the ERD image or a link to the ERD file here.

---

## API Documentations

All API routes are prefixed with `/api`.
Click the following links to view docs of each resource:
- [Auth and Users endpoints](docs/auth/api.md)
- [Messages endpoints](docs/messages/api.md)

---

## How to Run Locally
1. Clone the repository
2. Run `composer install`
3. Copy the `.env.example` to `.env`, and adjust it accordingly.
4. Run the following commands to setup dependencies:
   ```bash
   composer install
   php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
   php artisan jwt:secret
   ```
5. Run migrations: `php artisan migrate`
6. Start the server: `php artisan serve`