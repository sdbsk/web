@extends('layout')
@section('content')
    @php
        $category = get_queried_object();
    @endphp
    @if($category instanceof WP_Term)
        <h1>{{ $category->name }}</h1>
        @foreach(get_categories(['parent' => $category->term_id]) as $child)
            <a href="{{ get_category_link($child) }}">{{ $child->name }} ({{ $child->count }})</a>
        @endforeach
    @endif
    @while(have_posts())
        @php the_post() @endphp
        @include('partials.content-' . get_post_type())
    @endwhile
    @php get_the_posts_navigation() @endphp
@endsection
