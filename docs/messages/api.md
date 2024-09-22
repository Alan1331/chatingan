# Messages API Documentation

This API allows users to manage messages between authenticated users and other users in the system. The endpoints include retrieving messages between users, sending new messages, updating existing messages, and deleting messages.

## Base URL

All API routes are prefixed with `/api`.

---

## Endpoints

### 1. **Get All Messages Between Users**

Fetches all messages between the authenticated user and a contact (another user). Messages are ordered by `created_at`.

- **URL**: `/api/messages/{contact-id}`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer your-jwt-token`
  - `Content-Type: application/json`
  
- **Path Parameter**:
  
  | Parameter      | Type    | Description                              |
  |----------------|---------|------------------------------------------|
  | `contact-id`   | integer | The ID of the other user (contact) with whom the authenticated user has exchanged messages. |

- **Response**:

  - **200 OK**: On successful retrieval of messages.
  
  **Example Response**:
  ```json
  {
      "John Doe": [
          {
              "id": 1,
              "body": "Hello Jane",
              "created_at": "2024-09-22T12:00:00"
          }
      ],
      "Jane Doe": [
          {
              "id": 2,
              "body": "Hi John",
              "created_at": "2024-09-22T12:03:00"
          }
      ]
  }
  ```

---

### 2. **Send a New Message**

Send a message from the authenticated user to another user.

- **URL**: `/api/messages`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer your-jwt-token`
  - `Content-Type: application/json`

- **Request Body**:
  
  | Field     | Type    | Description                              |
  |-----------|---------|------------------------------------------|
  | `receiver`| integer | The ID of the user who will receive the message. |
  | `body`    | string  | The content of the message.              |

- **Response**:

  - **201 Created**: On successful message creation.

  **Example Request**:
  ```json
  {
      "receiver": 2,
      "body": "Hello there!"
  }
  ```

  **Example Response**:
  ```json
  {
      "message": "Message sent successfully",
      "data": {
          "id": 1,
          "body": "Hello there!",
          "sender": 1,
          "receiver": 2,
          "created_at": "2024-09-22T12:00:00",
          "updated_at": "2024-09-22T12:00:00"
      }
  }
  ```

---

### 3. **Update a Message**

Update a message sent by the authenticated user.

- **URL**: `/api/messages/{message-id}`
- **Method**: `PUT`
- **Headers**:
  - `Authorization: Bearer your-jwt-token`
  - `Content-Type: application/json`

- **Path Parameter**:
  
  | Parameter      | Type    | Description                              |
  |----------------|---------|------------------------------------------|
  | `message-id`   | integer | The ID of the message to update.          |

- **Request Body**:
  
  | Field  | Type   | Description                              |
  |--------|--------|------------------------------------------|
  | `body` | string | The updated content of the message.       |

- **Response**:

  - **200 OK**: On successful message update.

  **Example Request**:
  ```json
  {
      "body": "Updated message content"
  }
  ```

  **Example Response**:
  ```json
  {
      "message": "Message updated successfully",
      "data": {
          "id": 1,
          "body": "Updated message content",
          "sender": 1,
          "receiver": 2,
          "created_at": "2024-09-22T12:00:00",
          "updated_at": "2024-09-22T12:30:00"
      }
  }
  ```

- **Error**:
  - **403 Forbidden**: If the authenticated user is not the sender of the message.
  
  **Example Error Response**:
  ```json
  {
      "error": "Unauthorized"
  }
  ```

---

### 4. **Delete a Message**

Delete a message sent by the authenticated user.

- **URL**: `/api/messages/{message-id}`
- **Method**: `DELETE`
- **Headers**:
  - `Authorization: Bearer your-jwt-token`
  - `Content-Type: application/json`

- **Path Parameter**:
  
  | Parameter      | Type    | Description                              |
  |----------------|---------|------------------------------------------|
  | `message-id`   | integer | The ID of the message to delete.         |

- **Response**:

  - **200 OK**: On successful message deletion.

  **Example Response**:
  ```json
  {
      "message": "Message deleted successfully"
  }
  ```

- **Error**:
  - **403 Forbidden**: If the authenticated user is not the sender of the message.

  **Example Error Response**:
  ```json
  {
      "error": "Unauthorized"
  }
  ```

---

## Error Responses

- **401 Unauthorized**: If the JWT token is invalid or missing.
- **403 Forbidden**: If the authenticated user is not authorized to perform the action (e.g., updating or deleting someone else’s message).
- **404 Not Found**: If the message or user does not exist.

---

### Common Response Status Codes

- **200 OK**: Request was successful.
- **201 Created**: Resource was successfully created.
- **403 Forbidden**: The action is not allowed for the authenticated user.
- **404 Not Found**: Resource not found.
- **500 Internal Server Error**: An unexpected error occurred on the server.

---

## Testing the API

You can test these API routes using a tool like [Postman](https://www.postman.com/) or [cURL](https://curl.se/). Ensure that you include the correct headers, particularly the `Authorization` header for protected routes.

For example, using cURL:

```bash
# Get messages between two users
curl -X GET http://your-domain/api/messages/2   -H "Authorization: Bearer your-jwt-token"   -H "Content-Type: application/json"

# Send a new message
curl -X POST http://your-domain/api/messages   -H "Authorization: Bearer your-jwt-token"   -H "Content-Type: application/json"   -d '{
    "receiver": 2,
    "body": "Hello!"
  }'

# Update a message
curl -X PUT http://your-domain/api/messages/1   -H "Authorization: Bearer your-jwt-token"   -H "Content-Type: application/json"   -d '{
    "body": "Updated message content"
  }'

# Delete a message
curl -X DELETE http://your-domain/api/messages/1   -H "Authorization: Bearer your-jwt-token"   -H "Content-Type: application/json"
```
