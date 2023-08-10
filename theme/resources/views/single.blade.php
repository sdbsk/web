@extends('layout')
@section('content')
    <article>
        <header>
            <h1>{{ the_title() }}</h1>
        </header>
        <div>
            @php the_content(); @endphp
        </div>
    </article>
@endsection
