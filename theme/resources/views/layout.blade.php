<header>
    @if (has_nav_menu('header'))
        <nav>
            {{ wp_nav_menu(['theme_location' => 'header']) }}
        </nav>
    @endif
    <button type="button" class="btn btn-dark">Podporte nás</button>
</header>
@php dynamic_sidebar('sidebar-promotion-bar') @endphp
<main>
    @yield('content')
</main>
<footer>
    @php dynamic_sidebar('sidebar-footer') @endphp
</footer>

