# Real-Time Chat Implementation Guide

This document outlines the technical process of building the WebSocket-based chat system for the GGBuddy platform.

## 1. Core Infrastructure
### WebSocket Server (Laravel Reverb)
- **Engine**: Laravel Reverb was installed as the first-party WebSocket server.
- **Protocol**: Utilizes the Pusher protocol for compatibility with Laravel Echo.
- **Configuration**: Set up in `.env` with `BROADCAST_CONNECTION=reverb`.

### Backend Components
- **Broadcasting Event**: `MessageSent` implements `ShouldBroadcastNow` to ensure instant delivery.
- **Channel Security**: `routes/channels.php` defines a private channel `chat.room.{roomId}` ensuring only the player and E-Buddy can access the messages.
- **Database Schema**: 
    - `chat_rooms`: Stores the relationship between a Player and an E-Buddy.
    - `messages`: Stores individual chat logs with `is_read` status and timestamps.

## 2. Frontend Components (Livewire & Alpine.js)
The chat interface is built using two reactive Livewire components:

### ChatList
- **Function**: Displays all active conversations for the authenticated user.
- **Logic**: Fetches rooms where the user is either the player or the E-Buddy, ordered by the most recent message timestamp.
- **Integration**: Embedded in both the main Chat page and the E-Buddy Dashboard.

### ChatBox
- **Function**: The main messaging interface.
- **Real-Time Listener**: Uses `Laravel Echo` to listen for the `MessageSent` event on private channels.
- **State Management**:
    - `messageText`: Bound to the input field using `wire:model`.
    - `sendMessage()`: Validates input, persists to database, clears the draft bar, and broadcasts the event.
- **UX**: Implemented auto-scroll using Alpine.js (`x-init="$el.scrollTop = $el.scrollHeight"`) to ensure the latest messages are always visible.

## 3. UI/UX Refinement
- **Theme**: Follows the "Midnight Navy" premium aesthetic with accent-colored bubbles.
- **Status Indicators**: Dynamic "Online" indicators and message timestamps.
- **Entry Points**: 
    - Direct "Message" button on E-Buddy profiles.
    - "Message Player" action within the Incoming Orders list.
- **Clean Filters**: Integrated sub-filters into the Orders dashboard with a minimal text-color selection state to maintain a clean "fused" UI.

## 4. How to Run
To maintain the real-time functionality, the following services must be running:
1. **Vite**: `npm run dev` (Compiles the Echo listeners).
2. **Reverb**: `php artisan reverb:start` (Handles the WebSocket connections).
3. **Queue**: (If using `ShouldBroadcast` instead of `ShouldBroadcastNow`).
