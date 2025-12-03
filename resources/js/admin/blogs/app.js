/**
 * Admin Blogs Application
 * Main entry point for admin blog management functionality
 */

import { AdminBlogsSearch } from './components/search.js';
import { BulkActions } from './components/bulk-actions.js';
import { AdminBlogActions } from './components/actions.js';

// Initialize all admin blog components when DOM is ready
$(function () {
    // Initialize search functionality
    new AdminBlogsSearch();

    // Initialize bulk actions functionality
    new BulkActions();

    // Initialize individual blog actions
    new AdminBlogActions();
});
