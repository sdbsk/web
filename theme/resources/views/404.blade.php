@extends('layout')
@section('content')
    @if (! have_posts())
        <h1>Stránka nenájdená</h1>
        @php get_search_form(); @endphp
    @endif
@endsection
