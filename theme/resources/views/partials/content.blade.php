<article @php post_class() @endphp>
    <header>
        <h2>
            <a href="{{ get_permalink() }}">{{ get_the_title() }}</a>
        </h2>
        @include('partials.entry-meta')
    </header>
    <div>
        @php the_excerpt() @endphp
    </div>
</article>
