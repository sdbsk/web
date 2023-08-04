<header>
    <a href="{{ home_url('/') }}">{{ get_bloginfo('name') }}</a>
    @if (has_nav_menu('primary_navigation'))
        <nav>
            {{ wp_nav_menu(['theme_location' => 'primary_navigation']) }}
        </nav>
    @endif
</header>
@php dynamic_sidebar('sidebar-promotion-bar') @endphp
<main>
    @yield('content')
</main>
<footer>
    @php dynamic_sidebar('sidebar-footer') @endphp
</footer>

