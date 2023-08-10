@extends('layout')
@section('content')
    <h1{!! is_front_page() ? ' class="d-none"' : '' !!}>{{ the_title() }}</h1>
    @while(have_posts())
        @php the_post(); the_content(); @endphp
    @endwhile
@endsection
