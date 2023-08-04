<form role="search" method="get" action="{{ home_url('/') }}">
    <label>
        <input type="search" value="{{ get_search_query() }}" name="s">
    </label>
    <button>Hľadať</button>
</form>
