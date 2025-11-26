<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes to blogs table
        Schema::table('blogs', function (Blueprint $table) {
            $table->index('user_id', 'blogs_user_id_index');
            $table->index('published_at', 'blogs_published_at_index');
            $table->index(['status', 'published_at'], 'blogs_status_published_index');
        });
        
        // Add fulltext index separately (requires InnoDB full-text support)
        DB::statement('ALTER TABLE blogs ADD FULLTEXT INDEX blogs_title_fulltext (title)');

        // Add indexes to blog_comments table
        Schema::table('blog_comments', function (Blueprint $table) {
            $table->index('blog_id', 'comments_blog_id_index');
            $table->index('user_id', 'comments_user_id_index');
            $table->index('parent_id', 'comments_parent_id_index');
            $table->index(['blog_id', 'parent_id'], 'comments_blog_parent_index');
        });

        // Add indexes to blog_likes table
        Schema::table('blog_likes', function (Blueprint $table) {
            $table->index(['likeable_type', 'likeable_id'], 'likes_likeable_index');
            $table->index('user_id', 'likes_user_id_index');
        });

        // Add indexes to blog_bookmarks table
        Schema::table('blog_bookmarks', function (Blueprint $table) {
            $table->index('blog_id', 'bookmarks_blog_id_index');
            $table->index('user_id', 'bookmarks_user_id_index');
        });

        // Add indexes to notifications table
        Schema::table('notifications', function (Blueprint $table) {
            $table->index('user_id', 'notifications_user_id_index');
            $table->index('read', 'notifications_read_index');
            $table->index(['user_id', 'read'], 'notifications_user_read_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove fulltext index first
        DB::statement('ALTER TABLE blogs DROP INDEX blogs_title_fulltext');
        
        // Remove indexes from blogs table
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropIndex('blogs_user_id_index');
            $table->dropIndex('blogs_published_at_index');
            $table->dropIndex('blogs_status_published_index');
        });

        // Remove indexes from blog_comments table
        Schema::table('blog_comments', function (Blueprint $table) {
            $table->dropIndex('comments_blog_id_index');
            $table->dropIndex('comments_user_id_index');
            $table->dropIndex('comments_parent_id_index');
            $table->dropIndex('comments_blog_parent_index');
        });

        // Remove indexes from blog_likes table
        Schema::table('blog_likes', function (Blueprint $table) {
            $table->dropIndex('likes_likeable_index');
            $table->dropIndex('likes_user_id_index');
        });

        // Remove indexes from blog_bookmarks table
        Schema::table('blog_bookmarks', function (Blueprint $table) {
            $table->dropIndex('bookmarks_blog_id_index');
            $table->dropIndex('bookmarks_user_id_index');
        });

        // Remove indexes from notifications table
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_user_id_index');
            $table->dropIndex('notifications_read_at_index');
            $table->dropIndex('notifications_user_read_index');
        });
    }
};
