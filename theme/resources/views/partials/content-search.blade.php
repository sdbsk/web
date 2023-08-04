<article @php post_class() @endphp>
    <header>
        <h2>
            <a href="{{ get_permalink() }}">{{ html_entity_decode(get_the_title()) }}</a>
        </h2>
        @includeWhen(get_post_type() === 'post', 'partials.entry-meta')
    </header>
    <div>
        @php the_excerpt() @endphp
    </div>
</article>
