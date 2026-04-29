# Plan Over the Week — GG Buddy

## Goal
Turn the current GG Buddy concept into a clear Laravel roadmap, then implement the MVP in the right order without losing scope.

## What needs to be done today
Today is about locking the scope, organizing the work, and preparing the project so development can start cleanly.

- [ ] Review the use cases and ERD and confirm the MVP scope for GG Buddy
- [ ] Compare the current Laravel models, migrations, and routes against the planned domain
- [ ] List the missing features and fix the naming issues that need attention first
- [ ] Define the order of implementation for authentication, profiles, matchmaking, orders, chat, and admin tools
- [ ] Decide which entities must be updated before any UI or API work starts
- [ ] Create the first implementation backlog from this plan

## This week
### Day 1 — Scope and foundation
- Confirm the product scope
- Audit the database schema and models
- Identify missing relationships and inconsistencies
- Prepare the implementation backlog

### Day 2 — Authentication and roles
- Finalize user registration and login flow
- Set up role-based access
- Validate user, player, eBuddy, and admin access rules

### Day 3 — Profiles and browsing
- Build player and eBuddy profile management
- Add game/rank/profile data handling
- Support browsing and filtering of eBuddies

### Day 4 — Orders, services, and payment flow
- Implement service offers
- Create order lifecycle handling
- Add payment structure and tracking

### Day 5 — Chat, sessions, and matchmaking
- Add session chat
- Track matchmaking queue and matches
- Handle session status and completion flow

### Day 6 — Admin moderation and reporting
- Add user and account management
- Handle reports and moderation actions
- Add dashboard statistics and oversight tools

### Day 7 — Testing and cleanup
- Run full validation on the implemented work
- Fix bugs and naming inconsistencies
- Update documentation and finish the project notes

## Rules for the week
- Keep changes aligned with the diagrams
- Build the foundation before adding features on top
- Do not add extra scope until the MVP path is complete
- Keep every new feature connected to the existing Laravel structure

## Notes
This file is the working plan for the project. The first priority is today’s checklist: scope, audit, priorities, and backlog.
