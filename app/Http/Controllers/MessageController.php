<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Get all messages between authenticated user and a contact.
     */
    public function getMessages($contactId)
    {
        $userId = auth()->id();

        // Fetch all messages between the authenticated user and the contact
        $messages = Message::where(function ($query) use ($userId, $contactId) {
                $query->where('sender', $userId)
                      ->where('receiver', $contactId);
            })
            ->orWhere(function ($query) use ($userId, $contactId) {
                $query->where('sender', $contactId)
                      ->where('receiver', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Format response with sender's name and message details
        $formattedMessages = [];
        foreach ($messages as $message) {
            $formattedMessages[$message->sender()->first()->name][] = [
                'id' => $message->id,
                'body' => $message->body,
                'created_at' => $message->created_at,
            ];
        }

        return response()->json($formattedMessages);
    }

    /**
     * Send a new message from authenticated user to another user.
     */
    public function sendMessage(Request $request)
    {
        $validatedData = $request->validate([
            'receiver' => 'required|exists:users,id',
            'body' => 'required|string',
        ]);

        // Create the message
        $message = Message::create([
            'body' => $validatedData['body'],
            'sender' => auth()->id(),
            'receiver' => $request->receiver,
        ]);

        return response()->json([
            'message' => 'Message sent successfully',
            'data' => $message,
        ], 201);
    }

    /**
     * Update a specific message sent by the authenticated user.
     */
    public function updateMessage(Request $request, Message $message)
    {
        $userId = auth()->id();

        // Check if the authenticated user is the sender of the message
        if ($message->sender != $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate and update the message
        $validatedData = $request->validate([
            'body' => 'required|string',
        ]);

        $message->update([
            'body' => $validatedData['body'],
        ]);

        return response()->json([
            'message' => 'Message updated successfully',
            'data' => $message,
        ]);
    }

    /**
     * Delete a specific message sent by the authenticated user.
     */
    public function deleteMessage(Message $message)
    {
        $userId = auth()->id();

        // Check if the authenticated user is the sender of the message
        if ($message->sender != $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Delete the message
        $message->delete();

        return response()->json([
            'message' => 'Message deleted successfully',
        ]);
    }
}
