<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $post['lead'] }} | CityZen</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=hanken-grotesk:700,800|inter:400,500,600,700,800,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    class="cz-dashboard-page cz-post-detail-page"
    data-dashboard-awal
    data-theme="light"
    data-dashboard-flash="{{ session('status') }}"
    data-report-url="{{ url('/places/create') }}"
>
    @php
        $user = session('cityzen_user', ['name' => 'CityZen User', 'email' => 'member@cityzen.local']);
        $nameParts = collect(explode(' ', trim($user['name'] ?? 'CityZen User')))->filter()->values();
        $initials = $nameParts->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('') ?: 'CZ';
        $currentAvatar = $user['avatar_path'] ?? null;
    @endphp

    <div class="cz-dash-shell">
        @include('partials.dashboard-sidebar', ['activeNav' => 'home'])

        <main class="cz-dash-feed cz-post-detail-main" id="post">
            <header class="cz-dash-feed-header cz-post-detail-header">
                <a class="cz-post-back" href="{{ url('/dashboard') }}" aria-label="Kembali ke dashboard">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5" /><path d="m12 19-7-7 7-7" /></svg>
                </a>
                <h1>Post</h1>
                <button class="cz-dash-theme-toggle" type="button" data-theme-toggle aria-pressed="false">
                    <span class="cz-theme-sun" aria-hidden="true"></span>
                    <span data-theme-label>Dark mode</span>
                </button>
            </header>

            <article class="cz-dash-post cz-post-detail-card">
                <div class="cz-dash-post-avatar" aria-hidden="true">
                    @if ($post['avatar_image'])
                        <img src="{{ route('users.avatar', $post['author_id']) }}" alt="">
                    @else
                        {{ $post['avatar'] }}
                    @endif
                </div>
                <div class="cz-dash-post-body">
                    <header class="cz-dash-post-meta">
                        <strong>{{ $post['author'] }}</strong>
                        <span>{{ $post['handle'] }}</span>
                        <span>{{ $post['time'] }}</span>
                    </header>

                    <p class="cz-post-detail-copy">
                        <strong>{{ $post['lead'] }}</strong> {{ $post['text'] }}
                        <span class="cz-dash-hashtag">#{{ $post['badge'] }}</span>
                    </p>

                    @if ($post['image'])
                        <figure class="cz-dash-post-image cz-post-detail-image">
                            <img src="{{ route('places.image', $post['id']) }}" alt="{{ $post['image_alt'] }}">
                        </figure>
                    @endif

                    <div class="cz-post-detail-meta">
                        @if ($post['timestamp'])
                            <span>{{ $post['timestamp'] }}</span>
                        @endif
                        <span>{{ $post['rating'] }} rating</span>
                        <span>{{ $post['reports'] }} reports</span>
                    </div>

                    <footer class="cz-dash-post-actions cz-post-detail-actions">
                        <button type="button" data-action-toast="Komentar tersedia di bawah post.">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v12H8l-4 4V5Z" /></svg>
                            <span>{{ $post['comments'] }}</span>
                        </button>
                        <form method="POST" action="{{ route('places.repost', $post['id']) }}" data-async-interaction>
                            @csrf
                            <button type="submit" data-repost-post aria-pressed="{{ $post['reposted'] ? 'true' : 'false' }}" class="{{ $post['reposted'] ? 'is-active' : '' }}">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17 2l4 4-4 4" /><path d="M3 11V9a3 3 0 0 1 3-3h15" /><path d="M7 22l-4-4 4-4" /><path d="M21 13v2a3 3 0 0 1-3 3H3" /></svg>
                                <span data-count="reposts">{{ $post['reposts'] }}</span>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('places.like', $post['id']) }}" data-async-interaction>
                            @csrf
                            <button type="submit" data-like-post aria-pressed="{{ $post['liked'] ? 'true' : 'false' }}" class="{{ $post['liked'] ? 'is-liked' : '' }}">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.8 8.6c0 5.4-8.8 10-8.8 10s-8.8-4.6-8.8-10a4.7 4.7 0 0 1 8.8-2.4 4.7 4.7 0 0 1 8.8 2.4Z" /></svg>
                                <span data-count="likes">{{ $post['likes'] }}</span>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('places.bookmark', $post['id']) }}" data-async-interaction>
                            @csrf
                            <button type="submit" data-bookmark-post aria-pressed="{{ $post['bookmarked'] ? 'true' : 'false' }}" class="{{ $post['bookmarked'] ? 'is-active' : '' }}">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 4h12v17l-6-4-6 4V4Z" /></svg>
                                <span data-count="bookmarks">{{ $post['bookmarks'] }}</span>
                            </button>
                        </form>
                        @unless ($post['owned'])
                            <a href="{{ route('reports.create', $post['id']) }}" class="is-report" aria-label="Report post">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4v16" /><path d="M5 5h12l-1.5 4L17 13H5" /></svg>
                                <span class="sr-only">Report</span>
                            </a>
                        @endunless
                    </footer>
                </div>
            </article>

            <section class="cz-post-reply-box" aria-label="Tulis review atau komentar">
                <span class="cz-dash-avatar cz-dash-avatar-photo">
                    @if ($currentAvatar)
                        <img src="{{ route('users.avatar', $user['id']) }}" alt="">
                    @else
                        {{ $initials }}
                    @endif
                </span>
                <form method="POST" action="{{ route('places.review', $post['id']) }}" class="cz-dash-review-form cz-post-detail-review-form">
                    @csrf
                    <fieldset class="cz-dash-rating" aria-label="Rating tempat publik">
                        <legend>Rating</legend>
                        @for ($rating = 5; $rating >= 1; $rating--)
                            <input id="detail-rating-{{ $rating }}" name="rating" type="radio" value="{{ $rating }}" required>
                            <label for="detail-rating-{{ $rating }}" title="{{ $rating }} dari 5">
                                <svg aria-hidden="true" viewBox="0 0 24 24">
                                    <path d="M20 4c-7.2.4-12.1 2.5-14.7 6.2C3 13.5 3.6 17 6.1 19.5c2.5 2.4 6 2.2 8.7-.3C17.8 16.4 19.5 11.4 20 4Z" />
                                    <path d="M6 19c2.8-4.8 6.1-7.9 10-9.3" />
                                </svg>
                                <span class="sr-only">{{ $rating }} dari 5</span>
                            </label>
                        @endfor
                    </fieldset>
                    <input name="review" maxlength="500" placeholder="Post your reply tentang tempat ini">
                    <button type="submit">Reply</button>
                </form>
            </section>

            <section class="cz-post-thread" aria-label="Komentar warga">
                <div class="cz-post-thread-heading">
                    <span>Community replies</span>
                    <strong>{{ $post['comments'] }} komentar</strong>
                </div>

                @forelse ($reviews as $review)
                    <article class="cz-post-reply">
                        <div class="cz-dash-post-avatar" aria-hidden="true">
                            @if ($review['avatar_image'])
                                <img src="{{ route('users.avatar', $review['user_id']) }}" alt="">
                            @else
                                {{ $review['avatar'] }}
                            @endif
                        </div>
                        <div class="cz-post-reply-body">
                            <header class="cz-dash-post-meta">
                                <strong>{{ $review['author'] }}</strong>
                                <span>{{ $review['handle'] }}</span>
                                <span>{{ $review['time'] }}</span>
                            </header>
                            <p>{{ $review['text'] }}</p>
                            <div class="cz-post-reply-rating" aria-label="{{ $review['rating'] }} dari 5">
                                @for ($leaf = 1; $leaf <= 5; $leaf++)
                                    <svg viewBox="0 0 24 24" class="{{ $leaf <= $review['rating'] ? 'is-filled' : '' }}" aria-hidden="true">
                                        <path d="M20 4c-7.2.4-12.1 2.5-14.7 6.2C3 13.5 3.6 17 6.1 19.5c2.5 2.4 6 2.2 8.7-.3C17.8 16.4 19.5 11.4 20 4Z" />
                                    </svg>
                                @endfor
                            </div>
                        </div>
                    </article>
                @empty
                    <article class="cz-post-empty-replies">
                        <h2>Belum ada komentar.</h2>
                        <p>Jadilah warga pertama yang memberi review dan konteks untuk tempat ini.</p>
                    </article>
                @endforelse
            </section>
        </main>

        <aside class="cz-dash-right-rail" aria-label="Related places">
            <label class="cz-dash-search">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7" /><path d="m16 16 4 4" /></svg>
                <input type="search" placeholder="Search CityZen" data-dashboard-search>
            </label>

            <section class="cz-dash-trending-card">
                <h2>Related Places</h2>
                <div class="cz-dash-trend-list">
                    @forelse ($related as $item)
                        <a class="cz-post-related-link" href="{{ route('places.show', $item->id) }}">
                            <span>{{ $item->category_name ?: 'Public Space' }}</span>
                            <strong>{{ $item->name }}</strong>
                            <small>{{ (int) $item->likes_count }} likes - {{ (int) $item->reviews_count }} reviews</small>
                        </a>
                    @empty
                        <p class="cz-dash-trend-empty">Belum ada post terkait.</p>
                    @endforelse
                </div>
            </section>

            <footer class="cz-dash-rail-footer">
                <a href="{{ url('/') }}">CityZen Charter</a>
                <a href="{{ url('/bookmarks') }}">Bookmarks</a>
                <a href="{{ url('/notifications') }}">Notifications</a>
                <span>&copy; {{ date('Y') }} CityZen Corp.</span>
            </footer>
        </aside>
    </div>
</body>
</html>
