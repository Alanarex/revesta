# Blog System

This document describes the blog system implemented in this repository (Revesta).

## Overview
A complete blog system has been implemented with features for guests, authenticated users and administrators. It includes:

- Posts (blogs) with statuses: draft, pending, published, rejected
- Nested comments with replies and likes
- Bookmarks
- Admin moderation (approve/reject) with bulk actions
- Notifications (background jobs dispatch)
- Rich text editor (Quill)

## Quick start
1. As guest: visit `/blogs` to browse published posts.
2. As logged-in user: create/edit blogs from your profile (profile -> Blogs tab).
3. As admin: visit `/admin/blogs` to review pending posts.

## Key Endpoints
- Public
  - GET /blogs (index)
  - GET /blogs/{blog} (show)

- Authenticated
  - GET /blogs/create, POST /blogs
  - GET /blogs/{blog}/edit, PUT /blogs/{blog}
  - DELETE /blogs/{blog}
  - POST /blogs/{blog}/comments, GET /blogs/{blog}/comments/load-more
  - POST /blogs/likes/toggle
  - POST /blogs/bookmarks/toggle

- Admin
  - GET /admin/blogs
  - GET /admin/blogs/{blog}
  - POST /admin/blogs/{blog}/approve
  - POST /admin/blogs/{blog}/reject
  - POST /admin/blogs/bulk-action
  - GET /admin/blogs/all-ids

## Files of interest
- Controllers: `app/Http/Controllers/BlogController.php`, `AdminBlogController.php`
- Services: `app/Services/BlogService.php`
- Repositories: `app/Repositories/BlogRepository.php`
- Views: `resources/views/blogs/*`, `resources/views/admin/blogs/*`
- JS: `resources/js/blogs/*`, `resources/js/admin-blogs-index.js`

## Notes on moderation & bulk actions
- Bulk actions endpoint (`/admin/blogs/bulk-action`) validates `blog_ids` and `action`.
- `getAllPendingIds` returns matching pending blog ids for "select all across pages" in admin UI.
- Be cautious with very large result sets; the endpoint returns all matching IDs and may be large.

## Security & Authorization
- All admin endpoints are protected by `IsAdmin` middleware and/or policy checks (Gate::authorize)
- Blog create/update uses FormRequests that include authorization and validation logic

## Where to look next
- To change notification behavior: `app/Jobs/*` and `app/Services/BlogService.php`
- To adjust admin filters or search: `app/Repositories/BlogRepository.php` and `resources/views/admin/blogs/index.blade.php`

---

For the full, expanded documentation see `BLOG_SYSTEM_DOCUMENTATION.md` at repository root.
