@extends('layout')
@section('content')
    <h1>Vyhľadávanie</h1>
    @if (! have_posts())
        <div>Nič sa nenašlo</div>
        @php get_search_form() @endphp
    @endif
    @while(have_posts())
        @php the_post() @endphp
        @include('partials.content-search')
    @endwhile
    @php get_the_posts_navigation() @endphp
@endsection
