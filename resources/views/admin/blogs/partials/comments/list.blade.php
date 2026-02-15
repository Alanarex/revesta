@php
    $level = $level ?? 0;
@endphp

@foreach ($comments as $comment)
    @include('admin.blogs.partials.comments.item', ['comment' => $comment, 'level' => $level])
@endforeach
