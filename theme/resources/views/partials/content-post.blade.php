<article>
    <header>
        <h2>
            <a href="{{ get_permalink() }}">{{ the_title('', '', false) }}</a>
        </h2>
    </header>
    <div>
        @php the_excerpt() @endphp
    </div>
</article>
