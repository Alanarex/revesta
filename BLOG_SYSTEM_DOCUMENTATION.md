# Blog System Documentation

## Overview
A complete blog system has been implemented for your Revesta application with features for guests, authenticated users, and administrators.

## Features Implemented

### 1. **Database Structure**
- **blogs**: Stores blog posts with title, description, content, status (draft/pending/published/rejected)
- **blog_likes**: Polymorphic likes for blogs and comments
- **blog_comments**: Hierarchical comments with replies
- **blog_bookmarks**: User bookmarks for blogs
- **notifications**: System notifications for users
- **users**: Added 'bio' field

### 2. **User Roles & Access**

#### Guest Users
- View published blogs on `/blogs`
- Search blogs
- Guest-specific navbar (logo + sign in button)
- Click "Write a blog" triggers SweetAlert to login/register

#### Authenticated Users
- Full navbar with notification bell
- Sidebar with "Blogs" link
- Create, edit, and delete own blogs
- Like blogs and comments
- Comment on blogs with nested replies
- Bookmark blogs
- View own profile with blog statistics

#### Admin Users
- All authenticated user features
- Additional sidebar link: "Gérer les blogs"
- Approve/reject pending blogs
- Delete any blog or comment
- View all user profiles and blogs

### 3. **Pages & Routes**

#### Public Routes
- `GET /blogs` - Blog index (guest/auth)
- `GET /blogs/{blog}` - Blog detail page

#### Authenticated Routes
- `GET /blogs/create` - Create blog form
- `POST /blogs` - Store new blog
- `GET /blogs/{blog}/edit` - Edit blog form
- `PUT /blogs/{blog}` - Update blog
- `DELETE /blogs/{blog}` - Delete blog
- `POST /blogs/{blog}/comments` - Add comment
- `DELETE /blogs/comments/{comment}` - Delete comment
- `POST /blogs/likes/toggle` - Like/unlike
- `POST /blogs/bookmarks/toggle` - Bookmark/unbookmark
- `GET /notifications` - Get notifications
- `POST /notifications/mark-all-read` - Mark all read

#### Admin Routes
- `GET /admin/blogs` - Pending blogs list
- `GET /admin/blogs/{blog}` - Review blog
- `POST /admin/blogs/{blog}/approve` - Approve blog
- `POST /admin/blogs/{blog}/reject` - Reject blog

### 4. **Profile Page Updates**

The profile page now has **tabs**:

#### Info Tab
- User information (first name, last name, email, phone, etc.)
- Added **Bio** field
- Password change
- Account deletion

#### Blogs Tab
**For Own Profile:**
- Two sub-tabs: "Publiés" and "Brouillons"
- Each blog shows: title, description, action buttons
- Actions: View, Copy Link, Edit, Delete
- Draft blogs show status badges (pending/rejected)

**For Other Users:**
- Shows only published blogs
- Actions: View, Copy Link
- Admin can also delete

#### Sidebar
- User initials and name
- Bio (if exists)
- Blog statistics:
  - Number of published blogs
  - Total likes received
  - Member since date
- Contact information

### 5. **Blog Features**

#### Blog Index
- Search bar (left)
- "Write a blog" button (right)
- Each blog card shows:
  - Author initials & name (clickable to profile)
  - Title & short description (clickable to blog)
  - Time posted (e.g., "2 days ago")
  - Number of likes & comments
  - Bookmark button
  - Share button (copy link)
  - Delete button (author/admin only)

#### Blog Detail Page
- Full blog content with rich text formatting
- Author info & publish time
- Action bar: Like, Comment, Bookmark, Share, Delete
- Like & comment counters are clickable
- Smooth scroll to comments on click

#### Comments Section
- Add comment form (with user initials)
- Nested replies (up to 3 levels with left margin)
- "Show replies" button (initially hidden)
- Load more comments (5 at a time)
- Load more replies (2 at a time)
- Each comment shows:
  - Author initials & name (clickable)
  - Comment text
  - Time posted
  - Like button with count
  - Reply button
  - Delete button (author/admin only)

#### Rich Text Editor
- Quill.js editor for blog content
- Formatting: Headers, bold, italic, underline
- Lists: ordered & unordered
- Links
- Clean formatting button

### 6. **Notifications**

#### Notification Bell (Navbar)
- Shows badge with unread count
- Auto-updates every 30 seconds
- Dropdown with notification list
- "Mark all as read" button

#### Notification Types
- `blog_pending`: Admin notified when blog submitted
- `blog_approved`: Author notified when blog approved
- `blog_rejected`: Author notified when blog rejected (with reason)
- `blog_deleted`: Author notified when admin deletes blog (with reason)
- `comment_deleted`: Author notified when admin deletes comment (with reason)

### 7. **Workflows**

#### Publishing a Blog
1. User creates blog
2. Clicks "Publier" → SweetAlert confirmation
3. Status: `pending`
4. Admin receives notification
5. Admin reviews in "Gérer les blogs"
6. Admin approves → Status: `published`, author notified
7. OR Admin rejects → Status: `rejected`, author notified with reason

#### Deleting Content
- **Author deletes own blog**: Immediate deletion
- **Admin deletes user's blog**: SweetAlert asks for reason → Notification sent
- **Comment deletion**: Same pattern

#### Bookmarking (Guest)
- Click bookmark → SweetAlert prompts to login/register
- After login, redirects back to blog index

### 8. **Layouts**

#### Guest Layout (`blog-guest.blade.php`)
- Simple navbar: Logo (left) + Sign in button (right)
- No sidebar
- Container-based content

#### Authenticated Layout (`blog.blade.php`)
- Full navbar with notifications
- Sidebar with navigation
- Same structure as main app

### 9. **JavaScript Features**

All interactions use AJAX for smooth UX:
- Like/unlike (instant toggle)
- Bookmark (instant toggle)
- Comments (dynamic loading)
- Delete confirmations (SweetAlert)
- Copy link (clipboard API)
- Form submissions (no page reload)

### 10. **Models & Relationships**

#### User Model
```php
- blogs()
- publishedBlogs()
- blogLikes()
- blogComments()
- blogBookmarks()
- notifications()
- unreadNotifications()
- isAdmin()
```

#### Blog Model
```php
- user()
- comments() // Top-level only
- allComments() // All comments
- likes()
- bookmarks()
- isLikedBy($userId)
- isBookmarkedBy($userId)
- scopePublished()
- scopeSearchByTitle()
```

#### BlogComment Model
```php
- blog()
- user()
- parent()
- replies()
- likes()
- isLikedBy($userId)
```

## Installation & Setup

All migrations have been created and run. The system is ready to use.

### Testing the System

1. **As Guest**: Visit `/blogs` to see the guest interface
2. **As User**: Login and create a blog from profile page
3. **As Admin**: Set a user's role to admin, then access `/admin/blogs`

### Key Files Created/Modified

**Controllers:**
- BlogController.php
- BlogCommentController.php  
- BlogLikeController.php
- BlogBookmarkController.php
- NotificationController.php
- AdminBlogController.php
- ProfileController.php (updated)

**Models:**
- Blog.php
- BlogLike.php
- BlogComment.php
- BlogBookmark.php
- Notification.php
- User.php (updated with relationships & bio)

**Views:**
- layouts/blog.blade.php
- layouts/blog-guest.blade.php
- partials/blog-navbar.blade.php
- partials/blog-sidebar.blade.php
- blogs/index.blade.php
- blogs/show.blade.php
- blogs/create.blade.php
- blogs/edit.blade.php
- blogs/partials/comments.blade.php
- blogs/partials/scripts.blade.php
- admin/blogs/index.blade.php
- admin/blogs/show.blade.php
- profile/edit.blade.php (updated with tabs)
- profile/partials/blogs-tab.blade.php
- profile/partials/sidebar.blade.php (updated)
- profile/partials/update-profile-form.blade.php (added bio)

**Routes:**
- routes/web.php (updated with all blog routes)

**Migrations:**
- create_blogs_table
- create_blog_likes_table
- create_blog_comments_table
- create_blog_bookmarks_table
- create_notifications_table
- add_bio_to_users_table

## Modern & Maintainable Code

The system follows Laravel best practices:
- ✅ RESTful routing
- ✅ Eloquent ORM with relationships
- ✅ Form validation
- ✅ AJAX for smooth UX
- ✅ SweetAlert2 for beautiful alerts
- ✅ Bootstrap 5 for responsive design
- ✅ Quill.js for rich text editing
- ✅ Clean separation of concerns
- ✅ Reusable components (partials)
- ✅ Proper authorization checks

## Next Steps

The system is fully functional. You may want to:
- Add email notifications
- Implement blog categories/tags
- Add image upload for blogs
- Implement blog search with filters
- Add pagination controls styling
- Create factory/seeders for testing data
