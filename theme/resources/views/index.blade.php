@extends('layout')
@section('content')
    <h1>{{ get_the_title() }}</h1>
    @if (! have_posts())
        <div>Nič sa nenašlo</div>
        @php get_search_form() @endphp
    @endif
    @while(have_posts())
        @php the_post() @endphp
        @includeFirst(['partials.content-' . get_post_type(), 'partials.content'])
    @endwhile
    @php get_the_posts_navigation() @endphp
@endsection
