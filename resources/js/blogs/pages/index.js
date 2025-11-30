/**
 * Blogs Index Page
 * Handles the public blogs listing page functionality
 */

import { PublicBlogsSearch } from '../components/search.js';

// Initialize search functionality for the blogs index page
$(function() {
    // Only initialize if we're on the blogs index page
    if ($('#blogs-container').length > 0) {
        new PublicBlogsSearch();
    }
});