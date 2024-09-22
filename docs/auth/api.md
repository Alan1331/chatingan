
# Authentication API Documentation

This API provides user authentication using JWT tokens. The available endpoints include user registration, login, logout, and retrieving the authenticated user's profile.

## Base URL

All API routes are prefixed with `/api`.

---

## Endpoints

### 1. **User Registration**

Registers a new user and returns a JWT token.

- **URL**: `/api/register`
- **Method**: `POST`
- **Headers**: `Content-Type: application/json`
- **Request Body**:
  
  | Field          | Type    | Description                              |
  |----------------|---------|------------------------------------------|
  | `name`         | string  | The user's full name.                    |
  | `email`        | string  | The user's email (must be unique).       |
  | `password`     | string  | The user's password (min 6 characters).  |
  | `password_confirmation` | string | Confirmation of the password.      |
  | `address`      | string  | The user's address.                      |
  | `gender`       | boolean | The user's gender (true = male, false = female). |
  | `marital_status` | string | The user's marital status. Valid values: `single`, `married`, `divorced`, `widowed`. |

- **Response**:

  - **201 Created**: On successful registration.
  - **422 Unprocessable Entity**: If validation fails.

  ```json
  {
    "message": "User registered successfully",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john.doe@example.com",
      "address": "123 Main Street, Springfield",
      "gender": true,
      "marital_status": "single",
      "created_at": "2023-09-22T12:00:00Z",
      "updated_at": "2023-09-22T12:00:00Z"
    },
    "token": "your-jwt-token-here"
  }
  ```

---

### 2. **User Login**

Authenticates a user with their email and password, returning a JWT token.

- **URL**: `/api/login`
- **Method**: `POST`
- **Headers**: `Content-Type: application/json`
- **Request Body**:
  
  | Field      | Type   | Description                     |
  |------------|--------|---------------------------------|
  | `email`    | string | The user's email.               |
  | `password` | string | The user's password (min 6 characters). |

- **Response**:

  - **200 OK**: On successful login.
  - **401 Unauthorized**: If credentials are invalid.
  - **422 Unprocessable Entity**: If validation fails.

  ```json
  {
    "message": "Login successful",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john.doe@example.com",
      "address": "123 Main Street, Springfield",
      "gender": true,
      "marital_status": "single",
      "created_at": "2023-09-22T12:00:00Z",
      "updated_at": "2023-09-22T12:00:00Z"
    },
    "token": "your-jwt-token-here"
  }
  ```

---

### 3. **Logout**

Logs out the authenticated user by invalidating their JWT token.

- **URL**: `/api/logout`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer your-jwt-token`
  - `Content-Type: application/json`

- **Response**:

  - **200 OK**: On successful logout.
  - **500 Internal Server Error**: If token invalidation fails.

  ```json
  {
    "message": "User logged out successfully"
  }
  ```

---

### 4. **Get Authenticated User Profile**

Retrieves the profile of the currently authenticated user.

- **URL**: `/api/users/me`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer your-jwt-token`
  - `Content-Type: application/json`

- **Response**:

  - **200 OK**: On successful retrieval of the user profile.
  - **401 Unauthorized**: If the token is invalid or expired.

  ```json
  {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john.doe@example.com",
      "address": "123 Main Street, Springfield",
      "gender": true,
      "marital_status": "single",
      "created_at": "2023-09-22T12:00:00Z",
      "updated_at": "2023-09-22T12:00:00Z"
    }
  }
  ```

---

## Authentication

All routes, except `/api/login` and `/api/register`, require a valid JWT token to be passed in the `Authorization` header as follows:

```http
Authorization: Bearer your-jwt-token
```

The token is returned after successful registration or login and must be used for subsequent requests that require authentication.

---

## Error Responses

When validation fails, the API returns `422 Unprocessable Entity` with details about the validation errors. For example:

```json
{
  "email": [
    "The email must be a valid email address."
  ],
  "password": [
    "The password must be at least 6 characters."
  ]
}
```

In case of invalid tokens or unauthorized access, the API returns `401 Unauthorized`:

```json
{
  "message": "Unauthenticated."
}
```

---

### **Common Response Status Codes**

- **200 OK**: Request was successful.
- **201 Created**: Resource was successfully created.
- **401 Unauthorized**: Authentication failed (invalid or missing token).
- **422 Unprocessable Entity**: Validation errors.
- **500 Internal Server Error**: Something went wrong on the server.

---

## Testing the API

You can test these API routes using a tool like [Postman](https://www.postman.com/) or [cURL](https://curl.se/). Ensure that you include the correct headers, particularly the `Authorization` header for protected routes.

For example, using cURL:

```bash
# Register a user
curl -X POST http://your-domain/api/register   -H "Content-Type: application/json"   -d '{
    "name": "John Doe",
    "email": "john.doe@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "address": "123 Main Street, Springfield",
    "gender": true,
    "marital_status": "single"
  }'

# Login to get a token
curl -X POST http://your-domain/api/login   -H "Content-Type: application/json"   -d '{
    "email": "john.doe@example.com",
    "password": "password123"
  }'

# Get the authenticated user's profile
curl -X GET http://your-domain/api/users/me   -H "Authorization: Bearer your-jwt-token"   -H "Content-Type: application/json"

# Logout
curl -X POST http://your-domain/api/logout   -H "Authorization: Bearer your-jwt-token"   -H "Content-Type: application/json"
```

---
