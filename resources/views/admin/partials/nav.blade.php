<nav class="cz-admin-nav" aria-label="Admin sections">
    <a class="{{ ($activeAdmin ?? '') === 'dashboard' ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}">Analytics</a>
    <a class="{{ ($activeAdmin ?? '') === 'reports' ? 'is-active' : '' }}" href="{{ route('admin.reports') }}">Reports</a>
    <a class="{{ ($activeAdmin ?? '') === 'places' ? 'is-active' : '' }}" href="{{ route('admin.places') }}">Places</a>
    <a class="{{ ($activeAdmin ?? '') === 'categories' ? 'is-active' : '' }}" href="{{ route('admin.categories') }}">Categories</a>
    <a class="{{ ($activeAdmin ?? '') === 'users' ? 'is-active' : '' }}" href="{{ route('admin.users') }}">Users</a>
</nav>
