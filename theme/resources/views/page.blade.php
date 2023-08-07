@extends('layout')
@section('content')
    <h1{!! is_front_page() ? ' class="d-none"' : '' !!}>{{ get_the_title() }}</h1>
    @while(have_posts())
        @php the_post() @endphp
        @include('partials.content-page')
    @endwhile
@endsection
