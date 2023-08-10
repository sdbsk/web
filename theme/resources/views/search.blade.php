@extends('layout')
@section('content')
    <h1>Vyhľadávanie</h1>
    @if (! have_posts())
        <div>Nič sa nenašlo</div>
        @php get_search_form(); @endphp
    @endif
    @while(have_posts())
        @php the_post(); @endphp
        <div>
            <h2>
                <a href="{{ get_permalink() }}">{{ the_title() }}</a>
            </h2>
            <div>@php the_excerpt(); @endphp</div>
        </div>
    @endwhile
    @php get_the_posts_navigation(); @endphp
@endsection
