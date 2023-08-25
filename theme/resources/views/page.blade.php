@extends('layout')
@section('content')
    <h1>{{ the_title() }}</h1>
    @while(have_posts())
        @php the_post(); the_content(); @endphp
    @endwhile
@endsection
