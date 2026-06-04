<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CityZen Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=hanken-grotesk:700,800|inter:400,500,600,700,800,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    class="cz-dashboard-page"
    data-dashboard-awal
    data-theme="light"
    data-dashboard-flash="{{ session('status') }}"
    data-report-url="{{ url('/places/create') }}"
>
    @php
        $user = session('cityzen_user', ['name' => 'CityZen User', 'email' => 'member@cityzen.local']);
        $nameParts = collect(explode(' ', trim($user['name'] ?? 'CityZen User')))->filter()->values();
        $initials = $nameParts->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('') ?: 'CZ';
        $handle = '@'.str(str($user['email'] ?? 'member@cityzen.local')->before('@'))->replace(['.', '_', '-'], ' ')->slug('_');
    @endphp

    <div class="cz-dash-shell">
        <aside class="cz-dash-sidebar" aria-label="CityZen navigation">
            <div class="cz-dash-brand-block">
                <a class="cz-dash-brand" href="{{ url('/') }}" aria-label="CityZen landing page">
                    <span class="cz-dash-brand-mark" aria-hidden="true">
                        <svg viewBox="0 0 24 24" role="img">
                            <path d="M18.5 4.7c-6.7.6-11.9 4-13.4 8.2-1.2 3.4.8 6.4 4.2 6.4 4.2 0 7.9-4.7 9.2-14.6Z" />
                            <path d="M7.5 15.5c2.8-.5 5.3-2.2 7.4-5.1" />
                        </svg>
                    </span>
                    <span>
                        <strong>CityZen</strong>
                        <small>Civic Control</small>
                    </span>
                </a>
            </div>

            <nav class="cz-dash-nav" aria-label="Dashboard menu">
                <a class="cz-dash-nav-link is-active" href="#home">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m4 11 8-7 8 7v8a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1v-8Z" /></svg>
                    <span>Home</span>
                </a>
                <a class="cz-dash-nav-link" href="{{ url('/explore') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15.5 8.5-2.1 5.9-5.9 2.1 2.1-5.9 5.9-2.1Z" /><circle cx="12" cy="12" r="9" /></svg>
                    <span>Explore</span>
                </a>
                <a class="cz-dash-nav-link" href="#notifications">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 9a6 6 0 1 0-12 0c0 7-3 7-3 7h18s-3 0-3-7Z" /><path d="M10 20a2 2 0 0 0 4 0" /></svg>
                    <span>Notifications</span>
                </a>
                <a class="cz-dash-nav-link" href="#bookmarks">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 4h12v17l-6-4-6 4V4Z" /></svg>
                    <span>Bookmarks</span>
                </a>
                <a class="cz-dash-nav-link" href="{{ url('/profile') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4" /><path d="M4 20c1.6-4 14.4-4 16 0" /></svg>
                    <span>Profile</span>
                </a>
                <a class="cz-dash-nav-link" href="{{ url('/admin/reports') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v16H4z" /><path d="M8 9h8" /><path d="M8 13h5" /><path d="M8 17h3" /></svg>
                    <span>Admin</span>
                </a>
            </nav>

            <div class="cz-dash-sidebar-bottom">
                <a class="cz-dash-report-button" href="{{ url('/places/create') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 11v3a2 2 0 0 0 2 2h2l4 4v-4h3l7 3V6l-7 3H5a2 2 0 0 0-2 2Z" /><path d="M14 9v7" /></svg>
                    <span>Report</span>
                </a>

                <div class="cz-dash-user-card">
                    <span class="cz-dash-avatar">{{ $initials }}</span>
                    <span class="cz-dash-user-copy">
                        <strong>{{ $user['name'] }}</strong>
                        <small>{{ $handle }}</small>
                    </span>
                    <form method="POST" action="{{ url('/logout') }}">
                        @csrf
                        <button class="cz-dash-icon-button" type="submit" aria-label="Logout">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8V5a1 1 0 0 0-1-1H5v16h8a1 1 0 0 0 1-1v-3" /><path d="M10 12h10" /><path d="m17 9 3 3-3 3" /></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="cz-dash-feed" id="home">
            <header class="cz-dash-feed-header">
                <h1>Home</h1>
                <button class="cz-dash-theme-toggle" type="button" data-theme-toggle aria-pressed="false">
                    <span class="cz-theme-sun" aria-hidden="true"></span>
                    <span data-theme-label>Dark mode</span>
                </button>
            </header>

            <div class="cz-dash-tabs" role="tablist" aria-label="Feed filter">
                <button class="is-active" type="button" role="tab" aria-selected="true">For You</button>
            </div>

            <section class="cz-dash-composer" aria-label="Create a CityZen post">
                <span class="cz-dash-avatar cz-dash-avatar-photo">{{ $initials }}</span>
                <div class="cz-dash-composer-content">
                    <button type="button" class="cz-dash-compose-prompt" data-action-toast="Composer ready.">
                        What's happening in your city?
                    </button>
                    <div class="cz-dash-composer-tools">
                        <button type="button" aria-label="Attach image" data-action-toast="Image upload prepared.">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v14H4V5Z" /><path d="m7 16 4-4 3 3 2-2 3 3" /><circle cx="8.5" cy="8.5" r="1.2" /></svg>
                        </button>
                        <button type="button" aria-label="Attach location" data-action-toast="Location picker prepared.">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s7-5.2 7-12A7 7 0 1 0 5 9c0 6.8 7 12 7 12Z" /><circle cx="12" cy="9" r="2.2" /></svg>
                        </button>
                        <button type="button" aria-label="Attach report data" data-action-toast="Report data prepared.">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 19V5" /><path d="M9 19V9" /><path d="M13 19V7" /><path d="M17 19v-5" /><path d="M3 19h19" /></svg>
                        </button>
                        <a href="{{ url('/places/create') }}" class="cz-dash-post-button">Post</a>
                    </div>
                </div>
            </section>

            <section class="cz-dash-post-list" id="explore" aria-label="CityZen feed">
                @forelse ($feedPosts as $post)
                    <article
                        class="cz-dash-post"
                        data-feed-post
                        data-title="{{ strtolower($post['author'].' '.$post['handle'].' '.$post['lead'].' '.$post['text'].' '.$post['badge']) }}"
                    >
                        <div class="cz-dash-post-avatar" aria-hidden="true">{{ $post['avatar'] }}</div>
                        <div class="cz-dash-post-body">
                            <header class="cz-dash-post-meta">
                                <strong>{{ $post['author'] }}</strong>
                                @if ($post['verified'])
                                    <span class="cz-dash-verified" aria-label="Verified account"></span>
                                @endif
                                <span>{{ $post['handle'] }}</span>
                                <span>{{ $post['time'] }}</span>
                            </header>
                            <p><strong>{{ $post['lead'] }}</strong> {{ $post['text'] }} <a href="#explore">#{{ $post['badge'] }}</a></p>

                            @if ($post['image'])
                                <figure class="cz-dash-post-image">
                                    <img src="{{ asset($post['image']) }}" alt="{{ $post['image_alt'] }}">
                                </figure>
                            @endif

                            <footer class="cz-dash-post-actions">
                                <button type="button" data-action-toast="Comment thread opened.">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v12H8l-4 4V5Z" /></svg>
                                    <span>{{ $post['comments'] }}</span>
                                </button>
                                <a href="{{ route('reports.create', $post['id']) }}" data-action-link>
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17 2l4 4-4 4" /><path d="M3 11V9a3 3 0 0 1 3-3h15" /><path d="M7 22l-4-4 4-4" /><path d="M21 13v2a3 3 0 0 1-3 3H3" /></svg>
                                    <span>{{ $post['reposts'] }}</span>
                                </a>
                                <form method="POST" action="{{ route('places.like', $post['id']) }}">
                                    @csrf
                                    <button type="submit" data-like-post aria-pressed="{{ $post['liked'] ? 'true' : 'false' }}" class="{{ $post['liked'] ? 'is-liked' : '' }}">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.8 8.6c0 5.4-8.8 10-8.8 10s-8.8-4.6-8.8-10a4.7 4.7 0 0 1 8.8-2.4 4.7 4.7 0 0 1 8.8 2.4Z" /></svg>
                                        <span>{{ $post['likes'] }}</span>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('places.bookmark', $post['id']) }}">
                                    @csrf
                                    <button type="submit" data-bookmark-post aria-pressed="{{ $post['bookmarked'] ? 'true' : 'false' }}" class="{{ $post['bookmarked'] ? 'is-active' : '' }}">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 4h12v17l-6-4-6 4V4Z" /></svg>
                                        <span class="sr-only">Bookmark</span>
                                    </button>
                                </form>
                                <button type="button" data-action-toast="Share sheet prepared.">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="18" cy="5" r="3" /><circle cx="6" cy="12" r="3" /><circle cx="18" cy="19" r="3" /><path d="m8.6 10.8 8-4.6" /><path d="m8.6 13.2 8 4.6" /></svg>
                                    <span class="sr-only">Share</span>
                                </button>
                            </footer>
                            <form method="POST" action="{{ route('places.review', $post['id']) }}" class="cz-dash-review-form">
                                @csrf
                                <select name="rating" aria-label="Rating" required>
                                    <option value="">Rating</option>
                                    <option value="5">5</option>
                                    <option value="4">4</option>
                                    <option value="3">3</option>
                                    <option value="2">2</option>
                                    <option value="1">1</option>
                                </select>
                                <input name="review" maxlength="500" placeholder="Tulis review singkat">
                                <button type="submit">Review</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <article class="cz-dash-empty" data-feed-post data-title="">
                        <h2>Belum ada ruang publik di database.</h2>
                        <p>Data feed akan muncul otomatis setelah tabel places terisi. Mulai dari tombol Report untuk menambahkan kontribusi pertama.</p>
                        <a href="{{ url('/places/create') }}">Tambah data pertama</a>
                    </article>
                @endforelse
            </section>
        </main>

        <aside class="cz-dash-right-rail" id="notifications" aria-label="Dashboard side panel">
            <label class="cz-dash-search">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7" /><path d="m16 16 4 4" /></svg>
                <input type="search" placeholder="Search CityZen" data-dashboard-search>
            </label>

            <section class="cz-dash-trending-card" id="bookmarks">
                <h2>Trending Locally</h2>
                <div class="cz-dash-trend-list">
                    @forelse ($trends as $trend)
                        <button
                            type="button"
                            data-trending-place="{{ $trend['title'] }}"
                            data-title="{{ strtolower($trend['topic'].' '.$trend['title'].' '.$trend['meta']) }}"
                        >
                            <span>{{ $trend['topic'] }} &middot; Trending</span>
                            <strong>{{ $trend['title'] }}</strong>
                            <small>{{ $trend['meta'] }}</small>
                        </button>
                    @empty
                        <p class="cz-dash-trend-empty">Belum ada data trending dari database.</p>
                    @endforelse
                </div>
                <button class="cz-dash-show-more" type="button" data-action-toast="More local trends prepared.">Show more</button>
            </section>

            <footer class="cz-dash-rail-footer">
                <a href="{{ url('/') }}">CityZen Charter</a>
                <a href="#home">Privacy Policy</a>
                <a href="#home">Guidelines</a>
                <span>&copy; {{ date('Y') }} CityZen Corp.</span>
            </footer>
        </aside>
    </div>

    <div class="cz-dash-toast" role="status" aria-live="polite" data-dashboard-toast></div>
</body>
</html>
