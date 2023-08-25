@extends('layout')
@section('content')
    <div class="container-fluid">
        <h1>{{ the_title() }}</h1>
        @while(have_posts())
            @php the_post(); the_content(); @endphp
        @endwhile
    </div>
@endsection
