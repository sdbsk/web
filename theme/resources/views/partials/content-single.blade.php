<article @php post_class() @endphp>
    <header>
        <h1>{{ get_the_title() }}</h1>
        @include('partials.entry-meta')
    </header>
    <div>
        @php the_content() @endphp
    </div>
    <footer>
        @php wp_link_pages() @endphp
    </footer>
    @php comments_template() @endphp
</article>
