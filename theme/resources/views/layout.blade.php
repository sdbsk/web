<header>
    <div class="main-menu-wrapper">
        <div class="container-fluid">
            <div class="main-menu">
                <nav>
                    <button class="hamburger-menu"><img src="{{ asset('images/hamburger.svg') }}" alt="Menu"/></button>
                    <a class="logo" href="{{ get_site_url() }}"><img src="{{ asset('images/logo.svg') }}" alt="Saleziáni"/></a>
                    @if (has_nav_menu('header'))
                        {{ wp_nav_menu(['theme_location' => 'header']) }}
                    @endif
                </nav>
                <button type="button" class="btn-orange">Podporte nás</button>
            </div>
        </div>
    </div>
</header>
@php dynamic_sidebar('sidebar-promotion-bar'); @endphp
<main>
    @yield('content')
</main>
<footer>
    @php dynamic_sidebar('sidebar-footer'); @endphp
</footer>

