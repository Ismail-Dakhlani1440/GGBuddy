# GGBuddy — Full Backend Documentation

> Every file, every function, every redirect. Written so you can explain the whole project from memory.

---

# MODELS (`app/Models`)

---

## User.php
**Functionality it relates to:** Authentication, Role Management, Ownership checks, Relations hub

| Function | What it does | Redirects / Returns |
|---|---|---|
| `isAdmin()` | Returns `true` if the user's role title is `'admin'` | `bool` |
| `isEBuddy()` | Returns `true` if the user's role title is `'ebuddy'` | `bool` |
| `isPlayer()` | Returns `true` if the user's role title is `'player'` | `bool` |
| `eBuddyOrFail()` | Checks that the user is an E-Buddy AND their profile is active. If not, aborts with HTTP 403 | Returns the `EBuddy` model instance |
| `ownsEBuddy($eBuddy)` | Checks if this user's `id` matches the `eBuddy->user_id` | `bool` |
| `ownsOrder($order)` | Checks if this user's `id` matches the `order->player_id` | `bool` |
| `role()` | Eloquent relation — BelongsTo `Role` | `Role` model |
| `eBuddy()` | Eloquent relation — HasOne `EBuddy` (linked via `user_id`) | `EBuddy` model |
| `gameProfiles()` | Eloquent relation — HasMany `PlayerGameProfile` | Collection of profiles |
| `games()` | Eloquent relation — HasManyThrough `Game` via `PlayerGameProfile` | Collection of games |
| `orders()` | Eloquent relation — HasMany `Order` where `player_id = user.id` | Collection of orders |
| `sentMessages()` | Eloquent relation — HasMany `Message` where `sender_id = user.id` | Collection of messages |
| `reviewsGiven()` | Eloquent relation — HasMany `Review` where `player_id = user.id` | Collection of reviews |
| `reportsMade()` | Eloquent relation — HasMany `Report` where `reporter_id = user.id` | Collection of reports |
| `reportsReceived()` | Eloquent relation — HasMany `Report` where `target_id = user.id` | Collection of reports |
| `chatRoomsAsPlayer()` | Eloquent relation — HasMany `ChatRoom` where `player_id = user.id` | Collection of chat rooms |
| `chatRoomsAsEBuddy()` | Eloquent relation — HasMany `ChatRoom` where `e_buddy_id = user.id` | Collection of chat rooms |
| `notificationSetting()` | Eloquent relation — HasOne `NotificationSetting` | `NotificationSetting` model |

---

## EBuddy.php
**Functionality it relates to:** E-Buddy Profile, Availability System, Rating System, Order Management

| Function | What it does | Redirects / Returns |
|---|---|---|
| `isActive()` | Returns `true` if `status === 'active'` | `bool` |
| `isPending()` | Returns `true` if `status === 'pending'` | `bool` |
| `isSuspended()` | Returns `true` if `status === 'suspended'` | `bool` |
| `refreshGlobalRating()` | Queries all related reviews, calculates the average rating (rounded to 2 decimal places), and saves it to `global_rating` | `void` — updates the DB record |
| `getSessionCount()` | Counts all orders with `status = 'completed'` | `int` |
| `getCompletionRate()` | Divides completed orders by total terminal orders (completed + refused + expired + cancelled). Returns 100 if no orders exist | `int` (percentage) |
| `getTotalEarnings()` | Sums `total_amount` of all orders with `status = 'paid'` | `float` |
| `isAvailableNow()` | First checks `status === 'active'`. Then checks if the current time falls within a `Schedual` slot. Then checks if there is an active `Unavailability` block right now. Returns `true` only if in schedule AND not blocked | `bool` |
| `user()` | Eloquent relation — BelongsTo `User` | `User` model |
| `services()` | Eloquent relation — HasMany `Service` | Collection of services |
| `scheduals()` | Eloquent relation — HasMany `Schedual` | Collection of schedule slots |
| `unavailabilities()` | Eloquent relation — HasMany `Unavailability` | Collection of unavailability blocks |
| `orders()` | Eloquent relation — HasMany `Order` where `e_buddy_id = user_id` | Collection of orders |
| `reviews()` | Eloquent relation — HasMany `Review` | Collection of reviews |
| `chatRooms()` | Eloquent relation — HasMany `ChatRoom` | Collection of chat rooms |

---

## Order.php
**Functionality it relates to:** Order Lifecycle, Payment Flow, Session Management

| Function | What it does | Redirects / Returns |
|---|---|---|
| `isPending()` | Returns `true` if `status === 'pending'` | `bool` |
| `isConfirmed()` | Returns `true` if `status === 'confirmed'` | `bool` |
| `isCompleted()` | Returns `true` if `status === 'completed'` | `bool` |
| `isRefused()` | Returns `true` if `status === 'refused'` | `bool` |
| `isPaid()` | Returns `true` if `status === 'paid'` | `bool` |
| `isCancelled()` | Returns `true` if `status === 'cancelled'` | `bool` |
| `isExpired()` | Returns `true` if `status === 'expired'` OR if it is pending and `expires_at` has passed | `bool` |
| `hasSessionEnded()` | Checks if the order is `paid` and if the current time is past `paid_at + hours` | `bool` |
| `canBeCompleted()` | Returns `true` only if both `isPaid()` and `hasSessionEnded()` are true | `bool` |
| `markAsExpired()` | Sets `status` to `'expired'` in the database | `void` |
| `confirm()` | Sets `status` to `'confirmed'` and creates/updates a `Payment` record with `status = 'processing'` | `void` |
| `refuse($reason)` | Sets `status` to `'refused'` and saves the `refuse_reason` text | `void` |
| `pay()` | Sets `status` to `'paid'`, sets `paid_at` to current timestamp, and updates the linked `Payment` to `'succeeded'` | `void` |
| `cancel()` | Sets `status` to `'cancelled'` and updates the linked `Payment` to `'canceled'` | `void` |
| `complete()` | Sets `status` to `'completed'` | `void` |
| `belongsToPlayer($user)` | Returns `true` if `player_id === $user->id` | `bool` |
| `belongsToEBuddy($user)` | Returns `true` if `e_buddy_id === $user->id` | `bool` |
| `isParticipant($user)` | Returns `true` if user is either the player or the e-buddy | `bool` |
| `isReviewed()` | Checks if a `Review` exists for this order | `bool` |
| `getChatRoomId()` | Looks for a `ChatRoom` shared between the player and e-buddy of this order | `int\|null` — the ChatRoom ID |
| `player()` | Eloquent relation — BelongsTo `User` as player | `User` model |
| `eBuddy()` | Eloquent relation — BelongsTo `EBuddy` | `EBuddy` model |
| `service()` | Eloquent relation — BelongsTo `Service` | `Service` model |
| `payment()` | Eloquent relation — HasOne `Payment` | `Payment` model |
| `review()` | Eloquent relation — HasOne `Review` | `Review` model |

---

## Service.php
**Functionality it relates to:** E-Buddy Service Listings, Orders, Browse

| Function | What it does | Redirects / Returns |
|---|---|---|
| `getRankAttribute()` | Accessor — finds the E-Buddy's `PlayerGameProfile` for this service's `game_id` and returns their `currentRank` or `peakRank` | `Rank` model or `null` |
| `eBuddy()` | Eloquent relation — BelongsTo `EBuddy` (using `e_buddy_id` → `user_id`) | `EBuddy` model |
| `game()` | Eloquent relation — BelongsTo `Game` | `Game` model |
| `orders()` | Eloquent relation — HasMany `Order` | Collection of orders |

---

## Game.php
**Functionality it relates to:** Game Catalog, Browse, Rank System, Player Library

| Function | What it does | Redirects / Returns |
|---|---|---|
| `ranksOrdered()` | Returns the `ranks()` relation ordered by `tier` ascending (Bronze first) | Query Builder |
| `ranks()` | Eloquent relation — HasMany `Rank` | Collection of ranks |
| `services()` | Eloquent relation — HasMany `Service` | Collection of services |
| `playerGameProfiles()` | Eloquent relation — HasMany `PlayerGameProfile` | Collection of profiles |

---

## Rank.php
**Functionality it relates to:** Game Rank Tiers, Player Profiles, Service Display

| Function | What it does | Redirects / Returns |
|---|---|---|
| `game()` | Eloquent relation — BelongsTo `Game` | `Game` model |
| `services()` | Eloquent relation — HasMany `Service` | Collection |
| `currentRankProfiles()` | Eloquent relation — HasMany `PlayerGameProfile` where `current_rank_id = rank.id` | Collection |
| `peakRankProfiles()` | Eloquent relation — HasMany `PlayerGameProfile` where `peak_rank_id = rank.id` | Collection |

---

## PlayerGameProfile.php
**Functionality it relates to:** User Game Library, Rank Tracking, Service Rank Display

| Function | What it does | Redirects / Returns |
|---|---|---|
| `hasNewPeak()` | Checks if `currentRank->tier > peakRank->tier` | `bool` |
| `syncPeakRank()` | If `hasNewPeak()` is true, updates `peak_rank_id` to match `current_rank_id` | `void` |
| `user()` | Eloquent relation — BelongsTo `User` | `User` model |
| `game()` | Eloquent relation — BelongsTo `Game` | `Game` model |
| `currentRank()` | Eloquent relation — BelongsTo `Rank` via `current_rank_id` | `Rank` model |
| `peakRank()` | Eloquent relation — BelongsTo `Rank` via `peak_rank_id` | `Rank` model |

---

## ChatRoom.php
**Functionality it relates to:** Real-Time Chat, Messaging System

| Function | What it does | Redirects / Returns |
|---|---|---|
| `hasParticipant($userId)` | Returns `true` if `player_id` or `e_buddy_id` equals the given user ID | `bool` |
| `authorizeParticipant($user)` | Calls `abort(403)` if the user is NOT a participant of this room | `void` or HTTP 403 |
| `player()` | Eloquent relation — BelongsTo `User` as player | `User` model |
| `eBuddy()` | Eloquent relation — BelongsTo `User` as e-buddy | `User` model |
| `messages()` | Eloquent relation — HasMany `Message` ordered by `sent_at` | Collection of messages |
| `latestMessage()` | Eloquent relation — HasMany `Message` latest first, limited to 1 (for chat list previews) | Collection (1 item) |

---

## Message.php
**Functionality it relates to:** Real-Time Chat, Messaging System

| Function | What it does | Redirects / Returns |
|---|---|---|
| `isSentBy($user)` | Returns `true` if `sender_id === $user->id` | `bool` |
| `chatRoom()` | Eloquent relation — BelongsTo `ChatRoom` | `ChatRoom` model |
| `sender()` | Eloquent relation — BelongsTo `User` via `sender_id` | `User` model |

---

## Report.php
**Functionality it relates to:** User Reporting, Admin Moderation

| Function | What it does | Redirects / Returns |
|---|---|---|
| `isOpen()` | Returns `true` if `status === 'pending'` | `bool` |
| `isResolved()` | Returns `true` if `status === 'resolved'` | `bool` |
| `resolve()` | Sets `status` to `'resolved'` and stamps `resolved_at` with the current time | `void` |
| `reporter()` | Eloquent relation — BelongsTo `User` via `reporter_id` | `User` model |
| `target()` | Eloquent relation — BelongsTo `User` via `target_id` | `User` model |

---

## Review.php
**Functionality it relates to:** Order Completion, E-Buddy Rating System

| Function | What it does | Redirects / Returns |
|---|---|---|
| `starsLabel()` | Returns a string of filled/empty stars e.g. `★★★☆☆` based on `rating` | `string` |
| `starsArray()` | Returns an array of 5 booleans — `true` for filled stars — useful for Blade loops | `array` (5 booleans) |
| `order()` | Eloquent relation — BelongsTo `Order` | `Order` model |
| `player()` | Eloquent relation — BelongsTo `User` via `player_id` | `User` model |
| `eBuddy()` | Eloquent relation — BelongsTo `EBuddy` via `e_buddy_id` → `user_id` | `EBuddy` model |

---

## Payment.php
**Functionality it relates to:** Order Payment Flow, Stripe Integration

| Function | What it does | Redirects / Returns |
|---|---|---|
| `isSucceeded()` | Returns `true` if `status === 'succeeded'` | `bool` |
| `isFailed()` | Returns `true` if `status === 'failed'` | `bool` |
| `isCanceled()` | Returns `true` if `status === 'canceled'` | `bool` |
| `isPending()` | Returns `true` if status is any of: `requires_payment_method`, `requires_action`, `processing` | `bool` |
| `isCleared()` | Returns `true` if `isSucceeded()` — meaning the payment went through | `bool` |
| `order()` | Eloquent relation — BelongsTo `Order` | `Order` model |

---

## Schedual.php
**Functionality it relates to:** E-Buddy Availability, Weekly Schedule Management

| Function | What it does | Redirects / Returns |
|---|---|---|
| `isActiveNow()` | Checks if today matches `day_of_week` (e.g. "Tuesday") AND the current time is between `start_time` and `end_time` | `bool` |
| `eBuddy()` | Eloquent relation — BelongsTo `EBuddy` via `e_buddy_id` → `user_id` | `EBuddy` model |

---

## Unavailability.php
**Functionality it relates to:** E-Buddy Availability Override, Vacation/Break Blocking

| Function | What it does | Redirects / Returns |
|---|---|---|
| `isActiveNow()` | Checks if the current timestamp falls between `start_datetime` and `end_datetime` (timezone-aware) | `bool` |
| `eBuddy()` | Eloquent relation — BelongsTo `EBuddy` | `EBuddy` model |

---

## NotificationSetting.php
**Functionality it relates to:** User Notification Preferences

| Function | What it does | Redirects / Returns |
|---|---|---|
| `user()` | Eloquent relation — BelongsTo `User` | `User` model |
> **Fields:** `browser_notifications` (boolean), `sound_enabled` (boolean)

---

## Role.php
**Functionality it relates to:** User Role System

| Function | What it does | Redirects / Returns |
|---|---|---|
| `isAdmin()` | Returns `true` if `title === 'admin'` | `bool` |
| `isEBuddy()` | Returns `true` if `title === 'ebuddy'` | `bool` |
| `isPlayer()` | Returns `true` if `title === 'player'` | `bool` |
| `users()` | Eloquent relation — HasMany `User` | Collection of users |
> **Note:** No timestamps on this model (`$timestamps = false`).

---

# POLICIES (app/Policies)

---

## OrderPolicy.php
**Functionality:** Order Access Control � who can view and manage orders

| Function | What it does | Returns |
|---|---|---|
| viewAny($user) | Everyone can access the orders index page | true |
| viewIncoming($user) | Only E-Buddies can view the incoming orders tab | bool � true if E-Buddy |
| viewOutgoing($user) | Everyone can have outgoing orders as a player | true |
| view($user, $order) | Only the player OR the e-buddy on this order can view it | bool � true if user.id matches order.e_buddy_id or order.player_id |
| update($user, $order) | Only the E-Buddy assigned to the order can accept/refuse/complete it | bool � true if user.id === order.e_buddy_id |

---

## ServicePolicy.php
**Functionality:** E-Buddy Service Management Authorization

| Function | What it does | Returns |
|---|---|---|
| viewAny($user) | Only E-Buddies can see the services management page | bool � true if E-Buddy |
| view($user, $service) | Everyone can view a service (used on Browse page) | true |
| create($user) | Only E-Buddies can create services | bool � true if E-Buddy |
| update($user, $service) | Only the E-Buddy who owns the service can update it | bool � true if user.id === service.e_buddy_id |
| delete($user, $service) | Only the E-Buddy who owns the service can delete it | bool � true if user.id === service.e_buddy_id |
| restore($user, $service) | Nobody can restore a soft-deleted service | false |
| forceDelete($user, $service) | Nobody can permanently delete a service | false |

---

## SchedualPolicy.php
**Functionality:** E-Buddy Schedule Slot Authorization

| Function | What it does | Returns |
|---|---|---|
| viewAny($user) | Only E-Buddies can view the schedule page | bool � true if E-Buddy |
| create($user) | Only E-Buddies can add new schedule slots | bool � true if E-Buddy |
| delete($user, $schedual) | Only the E-Buddy who owns the slot can delete it | bool � true if user.id === schedual.e_buddy_id |

---

## UnavailabilityPolicy.php
**Functionality:** E-Buddy Unavailability Block Authorization

| Function | What it does | Returns |
|---|---|---|
| viewAny($user) | Only E-Buddies can see their unavailability blocks | bool � true if E-Buddy |
| create($user) | Only E-Buddies can add unavailability blocks | bool � true if E-Buddy |
| delete($user, $unavailability) | Only the E-Buddy who owns the block can delete it | bool � true if user.id === unavailability.e_buddy_id |

---

## PlayerGameProfilePolicy.php
**Functionality:** User Game Library Authorization

| Function | What it does | Returns |
|---|---|---|
| create($user) | Any authenticated user can add a game to their library | true |
| delete($user, $profile) | Only the owner of the profile entry can remove the game | bool � true if user.id === profile.user_id |

---

## ReportPolicy.php
**Functionality:** User Reporting System Authorization

| Function | What it does | Returns |
|---|---|---|
| create($user, $	arget) | Blocks self-reporting AND blocks duplicate pending reports against the same target | bool � false if self-report or duplicate |
| viewReports($user, $	arget) | Returns true only if the viewer is NOT the target | bool |

---

# MIDDLEWARE (app/Http/Middleware)

---

## IsAdmin.php
**Functionality:** Blocks non-admin users from accessing admin routes

| Function | What it does | Result |
|---|---|---|
| handle($equest, $
ext) | Checks auth()->check() AND auth()->user()->isAdmin(). If either fails, immediately aborts | HTTP 403 Unauthorized � or passes to next middleware |

---

## RedirectAdmin.php
**Functionality:** Prevents admins from accessing player/e-buddy areas

| Function | What it does | Result |
|---|---|---|
| handle($equest, $
ext) | If the logged-in user is an admin, redirects them away with an error message. Otherwise continues normally | Redirects to admin.dashboard with error flash � or passes through |

---
