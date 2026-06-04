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
        data-dashboard-flash="{{ session('status') }}"
        data-report-url="{{ url('/places/create') }}"
    >
        @php
            $user = session('cityzen_user', [
                'name' => 'CityZen User',
                'email' => 'member@cityzen.local'
            ]);
        @endphp

        <aside class="cz-dash-sidebar" aria-label="Dashboard navigation">
            <a class="cz-dash-brand" href="{{ url('/') }}">CityZen</a>
            <nav>
                <a class="is-active" href="#overview">Overview</a>
                <a href="#places">Places</a>
                <a href="#reports">Reports</a>
                <a href="#trending">Trending</a>
                <a href="{{ url('/profile') }}">Profile</a>
            </nav>
            <form method="POST" action="{{ url('/logout') }}">
                @csrf
                <button class="cz-dash-logout" type="submit">Logout</button>
            </form>
        </aside>

        <main class="cz-dash-main">
            <header class="cz-dash-header" id="overview">
                <div>
                    <p>Welcome back, {{ $user['name'] }}</p>
                    <h1>Your sustainable city dashboard.</h1>
                </div>
                <a class="cz-dash-new-report" href="{{ url('/places/create') }}">New Report</a>
            </header>

            <section class="cz-dash-stats" aria-label="CityZen stats">
                <article><span>Places watched</span><strong>24</strong></article>
                <article><span>Reports sent</span><strong>12</strong></article>
                <article><span>Impact score</span><strong>91%</strong></article>
            </section>

            <section class="cz-dash-toolbar" id="places">
                <div>
                    <h2>Explore public spaces</h2>
                    <p>Track parks, plazas, and corridors shaped by citizen reports.</p>
                </div>
                <label class="cz-dash-search">
                    <span>Search</span>
                    <input type="search" placeholder="Search place or city" data-dashboard-search>
                </label>
            </section>

            <section class="cz-dash-grid" aria-live="polite">
                @foreach ($places as $place)
                    <article class="cz-dash-place-card" data-place-card data-title="{{ strtolower($place->name.' '.$place->city.' '.optional($place->category)->name) }}">
                        @if($place->image)
                            <img
                                src="{{ $place->image ? asset('cityzen-dashboard/'.$place->image) : asset('cityzen-dashboard/default.jpg') }}"
                                alt="{{ $place->name }}"
                            >
                        @else
                            <img src="{{ asset('cityzen-dashboard/default.jpg') }}" alt="Default Image">
                        @endif
                        <div class="cz-dash-place-body">
                            <div>
                                <span>{{ $place->status }}</span>

                                <h3>
                                    {{ $place->name }}
                                    <span class="place-rating">
                                        ⭐ {{ number_format($place->average_rating ?? 0, 1) }}
                                    </span>
                                </h3>

                                <p>{{ $place->city }}, {{ $place->province }}</p>
                            </div>
                            <dl>
                                <div>
                                    <dt>Likes</dt>
                                    <dd>{{ $place->likes_count }}</dd>
                                </div>

                                <div>
                                    <dt>Bookmarks</dt>
                                    <dd>{{ $place->bookmarks_count }}</dd>
                                </div>

                                <div>
                                    <dt>Reviews</dt>
                                    <dd>{{ $place->reviews_count }}</dd>
                                </div>

                                <div>
                                    <dt>Rating</dt>
                                    <dd>{{ number_format($place->average_rating, 1) }}</dd>
                                </div>

                                <div>
                                    <dt>Reports</dt>
                                    <dd>{{ $place->reports_count }}</dd>
                                </div>
                            </dl>
                            <div class="cz-dash-card-actions">
                                <form method="POST" action="{{ route('places.like', $place) }}">
                                    @csrf
                                    <button type="submit" class="feed-action">
                                        <i data-lucide="heart"></i>
                                        <span>{{ $place->likes_count ?? 0 }}</span>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('places.bookmark', $place) }}">
                                    @csrf
                                    <button type="submit" class="feed-action">
                                        <i data-lucide="bookmark"></i>
                                        <span>{{ $place->bookmarks_count ?? 0 }}</span>
                                    </button>
                                </form>
                                <button type="button" data-report-place>Report</button>
                            </div>

                            <form method="POST" action="{{ route('places.review', $place) }}" class="review-form">
                                @csrf

                                <select name="rating" required>
                                    <option value="">Rating</option>
                                    <option value="5">5 - Sangat bagus</option>
                                    <option value="4">4 - Bagus</option>
                                    <option value="3">3 - Cukup</option>
                                    <option value="2">2 - Kurang</option>
                                    <option value="1">1 - Buruk</option>
                                </select>

                                <input type="text" name="review" placeholder="Tulis review singkat...">

                                <button type="submit">Review</button>
                            </form> 
                        </div>
                    </article>
                @endforeach
            </section>

            <section class="cz-dash-bottom" id="reports">
                <article class="cz-dash-panel">
                    <h2>Recent report actions</h2>
                    @if (session('cityzen_last_report'))
                        <p class="cz-dash-report-note">
                            Last draft: {{ session('cityzen_last_report.place_name') }} &middot;
                            {{ session('cityzen_last_report.issue') }}
                        </p>
                    @endif
                    <div class="cz-dash-report-list">
                        <button type="button" data-report-action="air quality">Air quality check</button>
                        <button type="button" data-report-action="broken lighting">Broken lighting</button>
                        <button type="button" data-report-action="tree canopy">Tree canopy request</button>
                    </div>
                </article>
                <article class="cz-dash-trending" id="trending">
                    <h2>Trending now</h2>
                    <button type="button" data-trending-place="Riverfront Walk">Riverfront Walk cleanup</button>
                    <button type="button" data-trending-place="Solar Loop Plaza">Solar Loop weekend market</button>
                    <button type="button" data-trending-place="Urban Canopy Hub">Urban Canopy cooling route</button>
                </article>
            </section>
        </main>

        <div class="cz-dash-toast" role="status" aria-live="polite" data-dashboard-toast></div>
    </body>
    </html>
