# Profile System Architecture

## Overview
The profile system has been restructured with a unified approach, proper access control, and separate views for different contexts.

## Routes

### Authenticated User's Own Profile
- **URL**: `/profile`
- **Route Name**: `profile.edit`
- **Controller**: `ProfileController@edit`
- **View**: `resources/views/profile/index.blade.php`
- **Access**: Requires authentication
- **Features**: Full access to personal info, blogs (all statuses), and bookmarks

### Public Profile (View Other Users)
- **URL**: `/profile/{userId}`
- **Route Name**: `profile.show`
- **Controller**: `ProfileController@show`
- **View**: `resources/views/profile/show.blade.php`
- **Access**: Public (guests and authenticated users)
- **Features**: View published blogs only, user stats

## View Structure

### Main Views

1. **`profile/index.blade.php`** - Own profile page with tabs
   - Hero section
   - Info tab (with read/edit toggle)
   - Blogs tab (all statuses)
   - Bookmarks tab
   - Sidebar with stats

2. **`profile/show.blade.php`** - Public profile page
   - Hero section
   - Published blogs only
   - Sidebar with stats

### Partial Views

#### Hero
- **`profile/partials/hero.blade.php`**
- Displays profile header
- Adapts text based on `$isViewingOwnProfile`

#### Info Tab
- **`profile/partials/info-tab.blade.php`** - Main info tab container
  - Toggles between read and edit modes
  - Contains edit button (only shown to owner)
  
- **`profile/partials/info-read.blade.php`** - Read-only view
  - Personal information display
  - Security information
  - Styled info cards

- **`profile/partials/info-edit.blade.php`** - Edit mode
  - Includes update profile form
  - Includes update password form
  - Includes delete account form
  - Protected by `@can('update', $user)` directive

#### Blogs Tab
- **`profile/partials/blogs-tab.blade.php`**
- Own profile: Shows all blog statuses with filters
  - All blogs
  - Published
  - Pending
  - Drafts
  - Rejected
- Other's profile: Shows published blogs only

#### Bookmarks Tab
- **`profile/partials/bookmarks-tab.blade.php`**
- Shows user's bookmarked blogs
- Only accessible on own profile

#### Sidebar
- **`profile/partials/sidebar.blade.php`**
- User avatar and basic info
- Statistics:
  - Published blogs count
  - Total likes received
  - Total comments received
- Contact information

#### Forms
- **`profile/partials/update-profile-form.blade.php`**
- **`profile/partials/update-password-form.blade.php`**
- **`profile/partials/delete-account-form.blade.php`**

#### Other
- **`profile/partials/alerts.blade.php`** - Success/error messages
- **`profile/partials/scripts.blade.php`** - JavaScript includes
- **`profile/partials/styles.blade.php`** - Custom CSS

## Access Control

### Policy-Based Authorization
All edit operations are protected by `UserPolicy`:
- `update` - Can edit profile information
- `updatePassword` - Can change password
- `delete` - Can delete account

### Blade Directives
```blade
@can('update', $user)
    <!-- Edit button or form -->
@endcan
```

### Controller Checks
```php
Gate::authorize('update', $request->user());
```

## Data Flow

### Own Profile (`/profile`)
```
Controller → 
  - Fetch published blogs
  - Fetch draft/pending/rejected blogs
  - Calculate total likes
  - Calculate total comments
  - Fetch bookmarks
  → Pass to index.blade.php
```

### Public Profile (`/profile/{userId}`)
```
Controller → 
  - Fetch published blogs only
  - Calculate total likes
  - Calculate total comments
  - No bookmarks/drafts
  → Pass to show.blade.php
```

## Features

### Info Tab
- **Read Mode**: Display all user information
- **Edit Mode**: Forms to update profile, password, and delete account
- **Access Control**: Edit button only visible to profile owner
- **Toggle**: JavaScript-based smooth transition between modes

### Blogs Tab
- **Own Profile**: 
  - Filter by status (All, Published, Pending, Drafts, Rejected)
  - Edit and delete buttons on each blog card
  - "Write a blog" button
- **Public Profile**: 
  - Published blogs only
  - No edit/delete buttons

### Bookmarks Tab
- **Own Profile Only**: Shows saved/bookmarked blogs
- Empty state with link to blog listing

### Statistics
- Real-time calculation of:
  - Total published blogs
  - Total likes received across all published blogs
  - Total comments received across all published blogs

## UI/UX Improvements

1. **Tabbed Interface**: Bootstrap tabs for organized content
2. **Responsive Design**: Mobile-friendly layout
3. **Icon Integration**: Font Awesome icons throughout
4. **Empty States**: Friendly messages when no content
5. **Access Control**: Seamless permission handling
6. **Smooth Transitions**: JavaScript-powered mode switching

## JavaScript

Located in `profile/partials/info-tab.blade.php`:
- Toggle edit mode: `#edit-info-btn` click handler
- Cancel edit: `#cancel-edit-btn` click handler
- jQuery-based show/hide transitions

## Security

1. **Route Protection**: Authenticated middleware on `/profile`
2. **Policy Authorization**: Gate checks in controller
3. **Blade Directives**: `@can` checks in views
4. **CSRF Protection**: All forms include CSRF tokens
5. **Password Confirmation**: Required for account deletion

## Future Enhancements

- Ajax form submissions for better UX
- Profile picture upload
- Real-time stats updates
- Social media links
- Privacy settings
- Activity timeline
