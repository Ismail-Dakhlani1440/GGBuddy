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
**Functionality it relates to:** Order Payment Flow

> A lightweight audit record automatically managed by the `Order` model's lifecycle methods. The schema contains three columns: `order_id`, `amount` (decimal 10,2), and `status` (enum). No external payment gateway is involved.

| Function | What it does | Redirects / Returns |
|---|---|---|
| `isSucceeded()` | Returns `true` if `status === 'succeeded'` | `bool` |
| `isFailed()` | Returns `true` if `status === 'failed'` | `bool` |
| `isCanceled()` | Returns `true` if `status === 'canceled'` | `bool` |
| `isProcessing()` | Returns `true` if `status === 'processing'` (initial state set when E-Buddy confirms an order via `confirm()`) | `bool` |
| `order()` | Eloquent relation - BelongsTo `Order` | `Order` model |

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
| handle($
equest, $
ext) | Checks auth()->check() AND auth()->user()->isAdmin(). If either fails, immediately aborts | HTTP 403 Unauthorized � or passes to next middleware |

---

## RedirectAdmin.php
**Functionality:** Prevents admins from accessing player/e-buddy areas

| Function | What it does | Result |
|---|---|---|
| handle($
equest, $
ext) | If the logged-in user is an admin, redirects them away with an error message. Otherwise continues normally | Redirects to admin.dashboard with error flash � or passes through |

---

# REQUESTS (app/Http/Requests)

---

## Auth/RegisterRequest.php
**Functionality:** Registration form validation

| Method | What it does | Returns |
|---|---|---|
| authorize() | Always returns true � everyone can submit the register form | true |
| rules() | Validates: role (must be player or ebuddy), name (max 255), display_name (max 255), timezone (optional), avatar (optional), email (unique in users table), password (min 8, confirmed), bio (optional max 1000) | array of validation rules |

---

## Admin/GameRequest.php
**Functionality:** General admin game form validation (legacy base request)

| Method | What it does | Returns |
|---|---|---|
| authorize() | Only allows logged-in admins � checks auth()->check() AND isAdmin() | bool |
| rules() | Validates: title (required max 255), description (optional), cover (optional image max 2MB), ranks array with id/title/tier fields | array |

---

## Admin/StoreGameRequest.php
**Functionality:** Validating the create-new-game form submitted by admin

| Method | What it does | Returns |
|---|---|---|
| authorize() | Always true � route is protected by is.admin middleware | true |
| rules() | Validates: title (required, unique in games table), description (optional min 10 chars), cover (required image jpeg/png/jpg/webp max 5MB), ranks array � each rank needs title and tier (integer min 1), icon optional image max 1MB | array |

---

## Admin/UpdateGameRequest.php
**Functionality:** Validating the edit-game form submitted by admin

| Method | What it does | Returns |
|---|---|---|
| authorize() | Always true � route protected by middleware | true |
| rules() | Same as StoreGameRequest but: title unique rule IGNORES the current game being edited (prevents self-conflict), cover is now optional (not required), ranks can include an id field for existing ranks | array |

---

## Dashboard/ServiceRequest.php
**Functionality:** Validates the create/update service form for E-Buddies

| Method | What it does | Returns |
|---|---|---|
| authorize() | Only E-Buddies can submit this form � checks user()->isEBuddy() | bool |
| rules() | Validates: game_id (must exist in games table), title (required max 255), description (required max 1000), price (required numeric min 0) | array |

---

## Dashboard/StoreOrderRequest.php
**Functionality:** Validates placing a new order on a service

| Method | What it does | Returns |
|---|---|---|
| authorize() | Always true � auth middleware handles it | true |
| rules() | Validates: hours (required numeric min 1 max 24), message (optional string max 500) | array |

---

## Dashboard/RefuseOrderRequest.php
**Functionality:** Validates the E-Buddy's refusal reason when declining an order

| Method | What it does | Returns |
|---|---|---|
| authorize() | Always true � Gate authorization in the controller handles it | true |
| rules() | Validates: refuse_reason (required string min 5 max 255) | array |
| messages() | Custom messages: refuse_reason.required = Please provide a reason, refuse_reason.min = Must be at least 5 characters | array |

---

## Dashboard/StoreReviewRequest.php
**Functionality:** Validates the player's review submission after a completed session

| Method | What it does | Returns |
|---|---|---|
| authorize() | Always true � controller checks ownership manually | true |
| rules() | Validates: rating (required integer min 1 max 5), comment (optional string max 1000) | array |

---

## Dashboard/StoreReportRequest.php
**Functionality:** Validates filing a report against another user

| Method | What it does | Returns |
|---|---|---|
| authorize() | Always true � policy is checked in the controller | true |
| rules() | Validates: target_id (required, must exist in users table), reason (required string min 10 max 1000) | array |
| messages() | Custom: reason.min = Please provide a more detailed reason (at least 10 characters) | array |

---

## Dashboard/StoreGameRequest.php
**Functionality:** Validates adding a game to a user's personal library

| Method | What it does | Returns |
|---|---|---|
| authorize() | Always true � middleware handles auth | true |
| rules() | Validates: game_id (required, must exist in games table), rank_id (required, must exist in ranks table) | array |

---

## Dashboard/ScheduleRequest.php
**Functionality:** Validates adding a new weekly availability slot for an E-Buddy

| Method | What it does | Returns |
|---|---|---|
| authorize() | Only E-Buddies can submit � checks isEBuddy() | bool |
| rules() | Validates: day_of_week (required, must be one of the 7 day names), start_time (required H:i format), end_time (required H:i format, must be AFTER start_time) | array |

---

## Dashboard/UnavailabilityRequest.php
**Functionality:** Validates adding a blocked time period (e.g. vacation)

| Method | What it does | Returns |
|---|---|---|
| authorize() | Only E-Buddies can submit � checks isEBuddy() | bool |
| rules() | Validates: start_datetime (required date), end_datetime (required date, must be AFTER start_datetime), reason (optional max 255) | array |

---

## Dashboard/UpdateProfileRequest.php
**Functionality:** Validates the profile update form (shared by Player and E-Buddy)

| Method | What it does | Returns |
|---|---|---|
| authorize() | Always true � middleware handles auth | true |
| rules() | Validates: display_name (required max 255), timezone (required max 100), avatar (optional image file max 2MB), bio (optional max 1000), banner (optional image max 4MB, min dimensions 1200x400px), browser_notifications (optional bool), sound_enabled (optional bool) | array |

---

## RefuseOrderRequest.php (root � legacy/unused)
**Functionality:** An older version of the refuse order request at the root namespace

| Method | What it does | Returns |
|---|---|---|
| authorize() | Returns false � this request is BLOCKED by default (not used in active routes) | false |
| rules() | Validates: message (required string min 1 max 255) | array |

> NOTE: This is a legacy file. The active version is Dashboard/RefuseOrderRequest.php

---

# CONTROLLERS (app/Http/Controllers)

---

## Auth/LoginController.php
**Functionality:** User Authentication � Login

| Function | What it does | Redirects / Returns |
|---|---|---|
| show() | Displays the login form view | Returns view: auth.login |
| login($
equest) | Validates email+password. Attempts Auth::attempt(). If it fails, returns back with an error message on the email field. If it succeeds, regenerates the session. | Redirects to intended URL or dashboard route � or back with errors |

---

## Auth/RegisterController.php
**Functionality:** User Registration � creates User and optionally an EBuddy profile

| Function | What it does | Redirects / Returns |
|---|---|---|
| show() | Displays the registration form view | Returns view: auth.register |
| register($
equest) | Validates via RegisterRequest. Looks up the Role by title. Creates the User record. If role is ebuddy, also creates an EBuddy record with status=pending. Logs the new user in and regenerates session. | Redirects to intended URL or dashboard route |

---

## Auth/LogoutController.php
**Functionality:** User Session Termination

| Function | What it does | Redirects / Returns |
|---|---|---|
| logout($
equest) | Calls Auth::logout(), invalidates the session, and regenerates the CSRF token | Redirects to / (homepage) |

---

## Dashboard/DashboardController.php
**Functionality:** Main dashboard router � sends users to the right dashboard based on their role and status

| Function | What it does | Redirects / Returns |
|---|---|---|
| index() | Checks if user is suspended ? redirect to suspended page. Checks if E-Buddy and pending ? redirect to ebuddy.pending. Checks if E-Buddy and active ? redirect to ebuddy.dashboard. Checks if admin ? redirect to admin.dashboard. Default ? redirect to player.dashboard | Redirects to suspended / ebuddy.pending / ebuddy.dashboard / admin.dashboard / player.dashboard |
| ebuddyDashboard() | Aborts if user is not an E-Buddy. Loads the EBuddy profile and stats (session count, pending orders, total earnings). | Returns view: dashboards.ebuddy with stats |

---

## Dashboard/ProfileController.php
**Functionality:** Profile viewing, public profiles, and profile editing

| Function | What it does | Redirects / Returns |
|---|---|---|
| showProfile() | Loads the authenticated user, their game profiles with ranks, and (if E-Buddy) their reviews | Returns view: dashboards.profile.view |
| publicProfile($user) | If viewing own profile, redirects to private profile. Otherwise loads the target user's eBuddy services, game profiles and ranks | Redirects to profile route if self � otherwise returns view: dashboards.profile.public |
| editProfile() | Loads the current user and their eBuddy record for the edit form | Returns view: dashboards.profile.edit |
| updateProfile($
equest) | Handles avatar upload to storage/avatars. Updates display_name and timezone. Updates or creates NotificationSetting. If E-Buddy: handles banner upload to storage/banners and updates bio. | Redirects to profile route with success flash |

---

## Dashboard/ServiceController.php
**Functionality:** E-Buddy Service Listings � create and delete services

| Function | What it does | Redirects / Returns |
|---|---|---|
| index() | Authorizes via ServicePolicy::viewAny. Loads games from the E-Buddy's personal library only. Loads all existing services for this E-Buddy. | Returns view: dashboards.services with games and services |
| store($
equest) | Authorizes via ServicePolicy::create. Creates a new Service linked to the E-Buddy's user_id. | Redirects back with success flash |
| destroy($service) | Authorizes via ServicePolicy::delete. Checks for any non-terminal orders (not completed/refused/cancelled/expired). Refuses deletion if active orders exist. Otherwise soft-deletes the service. | Redirects back with success or error flash |

---

## Dashboard/OrderController.php
**Functionality:** Full order lifecycle management � place, accept, refuse, pay, cancel, complete

| Function | What it does | Redirects / Returns |
|---|---|---|
| store($
equest, $service) | Checks if the E-Buddy isAvailableNow(). If not, returns with error. Creates a new Order with status=pending, calculates total_amount (price x hours), sets expires_at to 1 hour from now. | Redirects back with success or error flash |
| index($
equest) | Reads type query param (incoming/outgoing) and status filter. Authorizes via OrderPolicy. Queries orders for the right side (e_buddy_id or player_id). Applies status filter. Eager loads player, eBuddy.user, service.game, payment. | Returns view: dashboards.orders |
| accept($order) | Authorizes via OrderPolicy::update (must be the E-Buddy). Checks order isPending(). Calls order->confirm() which creates a Payment record. | Redirects back with success or error flash |
| refuse($order, $
equest) | Authorizes via OrderPolicy::update. Checks order isPending(). Calls order->refuse() with the reason from RefuseOrderRequest. | Redirects back with success or error flash |
| pay($order) | Checks auth user is the player (player_id). Checks order isConfirmed(). Calls order->pay() which sets paid_at and updates Payment to succeeded. | Redirects back with success or error flash |
| cancel($order) | Checks auth user is the player. Checks order is confirmed OR pending. Calls order->cancel() which updates Payment to canceled. | Redirects back with success or error flash |
| complete($order) | Authorizes via OrderPolicy::update (must be E-Buddy). Checks order isPaid(). Checks hasSessionEnded() � if not ended, returns an error showing remaining time. Calls order->complete(). | Redirects back with success or error flash |

---

## Dashboard/ScheduleController.php
**Functionality:** E-Buddy weekly schedule and unavailability block management

| Function | What it does | Redirects / Returns |
|---|---|---|
| index() | Authorizes via SchedualPolicy::viewAny. Loads all schedule slots ordered by day then time. Loads all future unavailability blocks. | Returns view: dashboards.schedule |
| storeSchedule($
equest) | Authorizes via SchedualPolicy::create. Creates a new Schedual record for the E-Buddy. | Redirects back with success flash |
| destroySchedule($schedual) | Authorizes via SchedualPolicy::delete. Deletes the schedule slot. | Redirects back with success flash |
| storeUnavailability($
equest) | Authorizes via UnavailabilityPolicy::create. Creates a new Unavailability block. | Redirects back with success flash |
| destroyUnavailability($unavailability) | Authorizes via UnavailabilityPolicy::delete. Deletes the unavailability block. | Redirects back with success flash |

---

## Dashboard/GameLibraryController.php
**Functionality:** Adding and removing games from a user's personal game library

| Function | What it does | Redirects / Returns |
|---|---|---|
| addGame() | Loads all available games with their ranks. Also loads the IDs of games the user already has to prevent duplicates in the form. | Returns view: dashboards.profile.add-game |
| storeGame($
equest) | Uses updateOrCreate to add or update a PlayerGameProfile for the user+game combination. Sets both current_rank_id and peak_rank_id to the chosen rank. | Redirects to profile route with success flash |
| removeGame($profile) | Authorizes via PlayerGameProfilePolicy::delete. If the user is an E-Buddy with active services for this game, blocks the deletion. Otherwise deletes the game profile. | Redirects back with success or error flash |

---

## Dashboard/ReviewController.php
**Functionality:** Player review submission for completed sessions

| Function | What it does | Redirects / Returns |
|---|---|---|
| store($
equest, $order) | Checks the auth user is the player of the order (HTTP 403 if not). Checks the order isCompleted() � error if not. Checks isReviewed() � error if already reviewed. Creates a Review record. Calls eBuddy->refreshGlobalRating() to recalculate the E-Buddy's rating. | Redirects back with success flash |

---

## Dashboard/ReportController.php
**Functionality:** Filing a formal complaint against another user

| Function | What it does | Redirects / Returns |
|---|---|---|
| store($
equest) | Validates target_id (must exist) and reason (min 5 chars). Creates a new Report with status=pending, reporter_id = auth user, target_id from form. | Redirects back with success flash |

---

## Admin/AdminController.php
**Functionality:** Admin dashboard, user management, report resolution, E-Buddy approvals

| Function | What it does | Redirects / Returns |
|---|---|---|
| dashboard() | Counts pending E-Buddy applications, pending reports, and total users | Returns view: dashboards.admin.overview |
| indexUsers() | Loads all users except the admin themselves, paginated 20 per page, with their role | Returns view: dashboards.admin.users.index |
| indexReports() | Loads all pending reports with reporter and target user data, paginated 20 per page | Returns view: dashboards.admin.reports.index |
| showReport($
eport) | Loads a single report with reporter and target | Returns view: dashboards.admin.reports.show |
| dismissReport($
eport) | Sets report status to resolved and stamps resolved_at | Redirects to admin.reports.index with success flash |
| toggleSuspension($user) | Blocks self-suspension. Flips the is_suspended boolean on the user and saves. | Redirects back with success flash showing suspended/unsuspended |
| ebuddyApplications() | Loads all E-Buddies with status=pending, with their user, paginated 15 per page | Returns view: dashboards.admin.ebuddies.index |
| approveEBuddy($ebuddy) | Sets E-Buddy status to active | Redirects to admin.ebuddies.index with success flash |
| rejectEBuddy($ebuddy) | Sets E-Buddy status to rejected | Redirects to admin.ebuddies.index with success flash |

---

## Admin/GameController.php
**Functionality:** Full CRUD for the game catalog including rank tier management

| Function | What it does | Redirects / Returns |
|---|---|---|
| index() | Loads all games with rank count, paginated 12 per page | Returns view: dashboards.admin.games.index |
| create() | Shows the create game form | Returns view: dashboards.admin.games.create |
| store($
equest) | Wraps everything in a DB transaction. Uploads cover image to storage/games. Creates the Game record. If ranks are submitted, loops through each rank, uploads icon to storage/ranks, creates each Rank. Commits or rolls back on failure. | Redirects to admin.games.index with success � or back with error flash |
| edit($game) | Loads the game and its ranks | Returns view: dashboards.admin.games.edit |
| update($
equest, $game) | DB transaction. Updates game title/description. If new cover uploaded, deletes old one from storage. Loops through ranks: if existing rank (has id), updates it; if new rank (no id), creates it. Deletes old rank icons when a new one is uploaded. | Redirects to admin.games.index with success � or back with error |
| destroy($game) | Soft-deletes the game (SoftDeletes trait) | Redirects to admin.games.index with success flash |
| destroyRank($
ank) | Deletes the rank icon from storage if it exists. Deletes the Rank record. | Redirects back with success flash |

---

## BrowseController.php
**Functionality:** The marketplace � discover and view E-Buddy profiles

| Function | What it does | Redirects / Returns |
|---|---|---|
| index() | Loads all users who are role=ebuddy, are not the current user, whose eBuddy status is active, and who have at least one service. Eager loads eBuddy.services.game, gameProfiles with ranks. | Returns view: browse.index |
| show($ebuddy) | Loads the E-Buddy user's full profile: eBuddy services, reviews with player info, game profiles with ranks | Returns view: browse.show |

---

## ChatController.php
**Functionality:** Real-time chat room access and initialization

| Function | What it does | Redirects / Returns |
|---|---|---|
| index($
oomId) | If a roomId is given, finds the ChatRoom and authorizes the user is a participant (403 if not). Passes the active room to the view. | Returns view: chat.index |
| start($userId) | Checks the user isn't chatting with themselves. Looks for an existing ChatRoom between the two users (checks both orderings). If none exists, creates one. | Redirects to chat route with the room ID |

---

# EVENTS (app/Events)

---

## MessageSent.php
**Functionality:** Real-time chat � fires when a new message is saved

| Function | What it does | Broadcasts On |
|---|---|---|
| __construct($message) | Stores the Message model as a public property so it gets serialized with the broadcast | � |
| broadcastOn() | Finds the ChatRoom for this message. Determines the recipient. Returns TWO channels: the shared room channel AND the recipient's private user channel | Private channel: chat.room.{id} AND App.Models.User.{recipientId} |
| broadcastWith() | Returns the message data including the sender relation loaded | array with message object |

> Implements ShouldBroadcastNow � fires immediately without queuing

---

## MessageRead.php
**Functionality:** Real-time chat � fires when a user marks messages as read

| Function | What it does | Broadcasts On |
|---|---|---|
| __construct($
oomId, $
eaderId) | Stores the room ID and the ID of the user who read the messages | � |
| broadcastOn() | Broadcasts to the shared room channel so the sender's UI can update the read status | Private channel: chat.room.{roomId} |

> Implements ShouldBroadcastNow � fires immediately without queuing

---

# ROUTES (routes/web.php and routes/channels.php)

---

## routes/web.php � Route Map

### Guest Routes (only accessible if NOT logged in)
| Method | URL | Controller@Method | Route Name |
|---|---|---|---|
| GET | /login | LoginController@show | login |
| POST | /login | LoginController@login | login.post |
| GET | /register | RegisterController@show | register |
| POST | /register | RegisterController@register | register.post |

### Auth Routes (must be logged in)
| Method | URL | Controller@Method | Route Name |
|---|---|---|---|
| POST | /logout | LogoutController@logout | logout |
| GET | /dashboard | DashboardController@index | dashboard |
| GET | /admin | AdminController@dashboard | admin.dashboard |
| GET | /player | (closure) view dashboards.player | player.dashboard |
| GET | /ebuddy/dashboard | DashboardController@ebuddyDashboard | ebuddy.dashboard |
| GET | /ebuddy/pending | (closure) view dashboards.ebuddy-pending | ebuddy.pending |
| GET | /suspended | (closure) view auth.suspended | suspended |

### No-Admin Routes (auth + redirects admins away)
| Method | URL | Controller@Method | Route Name |
|---|---|---|---|
| GET | /profile | ProfileController@showProfile | profile |
| GET | /profile/edit | ProfileController@editProfile | profile.edit |
| POST | /profile/edit | ProfileController@updateProfile | profile.update |
| GET | /profile/add-game | GameLibraryController@addGame | profile.add-game |
| POST | /profile/add-game | GameLibraryController@storeGame | profile.store-game |
| DELETE | /profile/game/{profile} | GameLibraryController@removeGame | profile.remove-game |
| GET | /ebuddy/services | ServiceController@index | ebuddy.services |
| POST | /ebuddy/services | ServiceController@store | ebuddy.services.store |
| DELETE | /ebuddy/services/{service} | ServiceController@destroy | ebuddy.services.destroy |
| GET | /ebuddy/schedule | ScheduleController@index | ebuddy.schedule |
| POST | /ebuddy/schedule | ScheduleController@storeSchedule | ebuddy.schedule.store |
| DELETE | /ebuddy/schedule/{schedual} | ScheduleController@destroySchedule | ebuddy.schedule.destroy |
| POST | /ebuddy/unavailability | ScheduleController@storeUnavailability | ebuddy.unavailability.store |
| DELETE | /ebuddy/unavailability/{unavailability} | ScheduleController@destroyUnavailability | ebuddy.unavailability.destroy |
| GET | /orders | OrderController@index | orders |
| POST | /orders/{service}/store | OrderController@store | browse.order |
| POST | /orders/{order}/accept | OrderController@accept | orders.accept |
| POST | /orders/{order}/refuse | OrderController@refuse | orders.refuse |
| POST | /orders/{order}/pay | OrderController@pay | orders.pay |
| POST | /orders/{order}/cancel | OrderController@cancel | orders.cancel |
| POST | /orders/{order}/complete | OrderController@complete | orders.complete |
| POST | /orders/{order}/review | ReviewController@store | orders.review |
| GET | /browse | BrowseController@index | browse.index |
| GET | /browse/{ebuddy} | BrowseController@show | browse.show |
| GET | /chat/{roomId?} | ChatController@index | chat |
| GET | /chat/start/{userId} | ChatController@start | chat.start |
| POST | /report | ReportController@store | report.store |

### Admin Routes (auth + is.admin middleware, prefix: /admin)
| Method | URL | Controller@Method | Route Name |
|---|---|---|---|
| GET | /admin/overview | AdminController@dashboard | admin.dashboard |
| GET | /admin/users | AdminController@indexUsers | admin.users.index |
| GET | /admin/reports | AdminController@indexReports | admin.reports.index |
| GET | /admin/reports/{report} | AdminController@showReport | admin.reports.show |
| POST | /admin/reports/{report}/dismiss | AdminController@dismissReport | admin.reports.dismiss |
| POST | /admin/users/{user}/suspend | AdminController@toggleSuspension | admin.users.suspend |
| GET | /admin/ebuddies | AdminController@ebuddyApplications | admin.ebuddies.index |
| POST | /admin/ebuddies/{ebuddy}/approve | AdminController@approveEBuddy | admin.ebuddies.approve |
| GET | /admin/ebuddies/{ebuddy}/reject | AdminController@rejectEBuddy | admin.ebuddies.reject |
| GET | /admin/games | GameController@index | admin.games.index |
| GET | /admin/games/create | GameController@create | admin.games.create |
| POST | /admin/games | GameController@store | admin.games.store |
| GET | /admin/games/{game}/edit | GameController@edit | admin.games.edit |
| PUT | /admin/games/{game} | GameController@update | admin.games.update |
| DELETE | /admin/games/{game} | GameController@destroy | admin.games.destroy |
| DELETE | /admin/ranks/{rank} | GameController@destroyRank | admin.ranks.destroy |

---

## routes/channels.php � Broadcast Channel Authorization

| Channel | Authorization Logic |
|---|---|
| App.Models.User.{id} | User can only listen to their own private channel � checks user.id === id |
| chat.room.{roomId} | Finds the ChatRoom. Calls hasParticipant(user.id) � only the player or e-buddy of that room can subscribe |
| match.{matchId} | Legacy matchmaking channel � checks if user is a participant of the match, returns user display data if so |
| matchmaking.user.{userId} | User can only subscribe to their own matchmaking channel |

---

*End of GGBuddy Backend Documentation*
