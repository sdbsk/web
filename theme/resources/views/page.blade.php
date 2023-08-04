@extends('layout')
@section('content')
    <h1>{{ get_the_title() }}</h1>
    @while(have_posts())
        @php the_post() @endphp
        @includeFirst(['partials.content-page', 'partials.content'])
    @endwhile
@endsection
