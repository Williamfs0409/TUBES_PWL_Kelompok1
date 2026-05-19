<!DOCTYPE html>
@php
    $cityzenUser = session('cityzen_user');
    $protectedUrl = fn (string $path) => $cityzenUser ? url($path) : url('/login');
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CityZen | Co-creating Sustainable Cities</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=hanken-grotesk:700,800|inter:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cz-page" data-public-page>
    @include('partials.cityzen-nav')

    <main>
        <section class="cz-hero" id="home">
            <div class="cz-hero__copy">
                <p class="eyebrow">Crowdsourced public space platform</p>
                <h1>Co-creating <span>sustainable</span> cities.</h1>
                <p>Empowering citizens to map, report, and improve urban spaces through community action and transparent civic data.</p>
                <div class="cz-hero__actions">
                    <a class="button button--primary" href="{{ $cityzenUser ? url('/dashboard') : url('/register') }}">Join the Movement</a>
                    <a class="button button--secondary" href="{{ $protectedUrl('/places/create') }}">Start a Report</a>
                </div>
            </div>
            <div class="cz-hero__stage" aria-label="CityZen community preview">
                <article class="cz-floating-card cz-floating-card--park">
                    <span>Fasilkom-TI</span>
                    <strong>4.2</strong>
                    <small>67 reviews · Sustainability score high</small>
                    <div><span style="width: 84%"></span></div>
                </article>
                <article class="cz-floating-card cz-floating-card--spotlight">
                    <span>Community Spotlight</span>
                    <strong>@urban_pioneer</strong>
                    <small>Verified 20 new public spaces this month.</small>
                </article>
            </div>
        </section>

        <section class="cz-split-section" id="mission">
            <div>
                <p class="eyebrow">Our mission</p>
                <h2>Aligned with SDG 11: Sustainable Cities & Communities</h2>
                <p>CityZen bridges urban data and lived experience so communities can make public spaces more inclusive, safe, resilient, and sustainable.</p>
                <ul class="cz-check-list">
                    <li>Inclusive public space accessibility</li>
                    <li>Real-time environmental and safety reporting</li>
                    <li>Participatory community improvement tools</li>
                </ul>
            </div>
            <figure class="cz-image-card">
                <img alt="CityZen landing page preview" src="{{ asset('cityzen-ui/landing.png') }}">
                <figcaption>
                    <strong>Mission First</strong>
                    <span>SDG 11 in daily civic action</span>
                </figcaption>
            </figure>
        </section>

        <section class="cz-section" id="features">
            <div class="cz-section__head">
                <p class="eyebrow">Platform tools</p>
                <h2>Empowering Urban Change</h2>
                <p>Tools designed for modern citizens to influence their environment effectively.</p>
            </div>
            <div class="cz-feature-grid">
                <article class="cz-card">
                    <span class="cz-card__icon">◎</span>
                    <h3>Discover</h3>
                    <p>Explore parks, campuses, community spaces, accessibility routes, and public facilities near you.</p>
                    <a href="{{ $protectedUrl('/dashboard') }}">Explore places</a>
                </article>
                <article class="cz-card">
                    <span class="cz-card__icon cz-card__icon--warm">!</span>
                    <h3>Report</h3>
                    <p>Submit condition reports for damaged facilities, safety concerns, accessibility, or cleanliness issues.</p>
                    <a href="{{ $protectedUrl('/places/create') }}">Start a report</a>
                </article>
                <article class="cz-card">
                    <span class="cz-card__icon cz-card__icon--sage">≋</span>
                    <h3>Connect</h3>
                    <p>Join local communities, review spaces, and coordinate improvements with other city contributors.</p>
                    <a href="{{ $protectedUrl('/profile') }}">View profile</a>
                </article>
            </div>
        </section>

        <section class="cz-launch-band">
            <div>
                <p class="eyebrow">Now building</p>
                <h2>The first report can change how a city listens.</h2>
                <p>CityZen is starting from zero on purpose: every map pin, review, and report should come from real citizens who care about public spaces.</p>
            </div>
            <div class="cz-launch-grid">
                <article>
                    <span>01</span>
                    <strong>Seed the map</strong>
                    <p>Add the first public places worth protecting, improving, or celebrating.</p>
                </article>
                <article>
                    <span>02</span>
                    <strong>Turn concern into data</strong>
                    <p>Transform scattered complaints into structured civic signals.</p>
                </article>
                <article>
                    <span>03</span>
                    <strong>Build public memory</strong>
                    <p>Create a transparent record of what communities notice and need.</p>
                </article>
            </div>
        </section>

        <section class="cz-section" id="spaces">
            <div class="cz-section__head cz-section__head--split">
                <div>
                    <p class="eyebrow">Our canvas</p>
                    <h2>Transforming Public Spaces</h2>
                </div>
                <a class="button button--secondary" href="{{ $protectedUrl('/dashboard') }}">View All Projects</a>
            </div>
            <div class="cz-gallery">
                <article>
                    <img alt="CityZen landing page screen" src="{{ asset('cityzen-ui/landing.png') }}">
                    <div><strong>Lapangan Merdeka</strong><span>Medan, Indonesia</span></div>
                </article>
                <article>
                    <img alt="CityZen add public place screen" src="{{ asset('cityzen-ui/add-place.png') }}">
                    <div><strong>USU Roadwalk</strong><span>Medan, Indonesia</span></div>
                </article>
                <article>
                    <img alt="CityZen profile screen" src="{{ asset('cityzen-ui/profile.png') }}">
                    <div><strong>RingRoad City Walk Park</strong></div>
                </article>
                <article>
                    <img alt="CityZen admin dashboard screen" src="{{ asset('cityzen-ui/admin.png') }}">
                    <div><strong>Deli Park Biogarden</strong></div>
                </article>
            </div>
        </section>

        <section class="cz-section" id="team">
            <div class="cz-section__head">
                <p class="eyebrow">Team section</p>
                <h2>Built by Students, Designed for Better Cities</h2>
                <p>"Designing the cities we want to live in, one community at a time."</p>
            </div>
            <div class="cz-team-grid">
                @foreach ([
                    ['William Fransisco Sihotang', 'Project Manager'],
                    ['Ainuha Suraiya', 'Frontend Developer'],
                    ['Chyntia Rachel Anandita Hutabarat', 'Backend Developer'],
                    ['Felix Desselo Tambunan', 'UI/UX Designer'],
                    ['Hadziq Naufal Sinaga', 'System & DB Engineer'],
                ] as $member)
                    <article class="cz-team-card">
                        <span>{{ collect(explode(' ', $member[0]))->map(fn ($part) => $part[0])->join('') }}</span>
                        <h3>{{ $member[0] }}</h3>
                        <p>{{ $member[1] }}</p>
                    </article>
                @endforeach
            </div>
        </section>
    </main>

    <footer class="cz-footer">
        <div>
            <h2>CityZen</h2>
            <p>Building the infrastructure for civic participation and sustainable urban development, one neighborhood at a time.</p>
            <small>&copy; 2026 CityZen Civic Tech</small>
        </div>
        <nav>
            <a href="#mission">Sustainability Manifesto</a>
            <a href="#features">Urban Data Privacy</a>
            <a href="#team">Open Source</a>
            <a href="{{ $protectedUrl('/admin') }}">Admin Moderation</a>
        </nav>
    </footer>

    <div class="toast" id="toast" role="status" aria-live="polite"></div>
</body>
</html>
