<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CityZen | Co-creating Sustainable Cities</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=hanken-grotesk:700,800|inter:400,500,600,700,800,900" rel="stylesheet" />
    <style>
        :root {
            --surface: #f8f9fa;
            --surface-low: #f0f3f1;
            --ink: #191c1d;
            --muted: #4b5548;
            --line: #191c1d;
            --primary: #154212;
            --primary-2: #2d6429;
            --mint: #bcf0ae;
            --sage: #d1e8dd;
            --peach: #ffdbca;
            --shadow: 12px 12px 0 rgba(25, 28, 29, 0.1);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: var(--surface);
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, sans-serif;
            margin: 0;
            min-width: 320px;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .topbar {
            align-items: center;
            background: rgba(248, 249, 250, 0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1.5px solid var(--line);
            display: flex;
            gap: 24px;
            height: 64px;
            justify-content: space-between;
            padding: 0 clamp(20px, 5vw, 64px);
            position: sticky;
            top: 0;
            z-index: 20;
        }

        .brand {
            color: var(--primary);
            font-family: "Hanken Grotesk", Inter, sans-serif;
            font-size: 30px;
            font-weight: 800;
        }

        .nav {
            align-items: center;
            display: flex;
            gap: 18px;
        }

        .nav a {
            border-radius: 8px;
            color: var(--muted);
            font-size: 14px;
            font-weight: 800;
            padding: 7px 9px;
        }

        .nav a:hover,
        .nav a:focus-visible {
            background: #eaf4e7;
            color: var(--primary);
        }

        .button {
            align-items: center;
            border: 1.5px solid var(--line);
            border-radius: 999px;
            cursor: pointer;
            display: inline-flex;
            font-weight: 800;
            justify-content: center;
            min-height: 42px;
            padding: 10px 18px;
            transition: box-shadow 160ms ease, transform 160ms ease;
        }

        .button:hover,
        .button:focus-visible {
            box-shadow: 0 8px 20px rgba(25, 28, 29, 0.14);
            transform: translateY(-1px);
        }

        .button--primary {
            background: var(--primary-2);
            border-color: var(--primary-2);
            color: #ffffff;
        }

        .button--secondary {
            background: var(--surface);
        }

        .hero {
            align-items: center;
            background: linear-gradient(180deg, rgba(188, 240, 174, 0.32), rgba(248, 249, 250, 1));
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(300px, 0.8fr);
            min-height: calc(100vh - 64px);
            overflow: hidden;
            padding: clamp(64px, 9vw, 116px) clamp(20px, 5vw, 64px);
        }

        .hero-copy {
            max-width: 760px;
        }

        .eyebrow {
            color: #964817;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.14em;
            margin: 0 0 13px;
            text-transform: uppercase;
        }

        h1,
        h2,
        h3 {
            font-family: "Hanken Grotesk", Inter, sans-serif;
        }

        h1 {
            font-size: clamp(44px, 7vw, 84px);
            line-height: 0.98;
            margin: 0;
        }

        h1 span {
            background: rgba(188, 240, 174, 0.62);
            border-radius: 12px;
            color: var(--primary);
            display: inline-block;
            padding: 0 10px 6px;
        }

        .hero p:not(.eyebrow),
        .section-head p,
        .split p,
        .launch p {
            color: var(--muted);
            font-size: 18px;
            line-height: 1.55;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 30px;
        }

        .hero-stage {
            min-height: 470px;
            position: relative;
        }

        .floating-card {
            background: #ffffff;
            border: 1.5px solid var(--line);
            border-radius: 24px;
            box-shadow: var(--shadow);
            display: grid;
            gap: 10px;
            padding: 20px;
            position: absolute;
            width: min(280px, 80vw);
        }

        .floating-card:first-child {
            left: 0;
            top: 18%;
            transform: rotate(-3deg);
        }

        .floating-card:last-child {
            background: var(--mint);
            bottom: 18%;
            right: 0;
            transform: rotate(5deg);
        }

        .floating-card span {
            color: var(--primary);
            font-weight: 900;
        }

        .floating-card strong {
            font-family: "Hanken Grotesk", Inter, sans-serif;
            font-size: 28px;
        }

        .floating-card small {
            color: var(--muted);
        }

        .meter {
            background: #e7e8e9;
            border-radius: 999px;
            height: 9px;
            overflow: hidden;
        }

        .meter i {
            background: var(--primary);
            display: block;
            height: 100%;
            width: 84%;
        }

        .split,
        .section {
            margin: 0 auto;
            max-width: 1280px;
            padding: clamp(58px, 8vw, 88px) clamp(20px, 5vw, 64px);
        }

        .split {
            align-items: center;
            background: var(--surface-low);
            display: grid;
            gap: 38px;
            grid-template-columns: minmax(0, 1fr) minmax(320px, 0.9fr);
            max-width: none;
        }

        .split > * {
            margin-left: auto;
            margin-right: auto;
            max-width: 620px;
        }

        h2 {
            font-size: clamp(30px, 4vw, 42px);
            line-height: 1.08;
            margin: 0 0 12px;
        }

        .check-list {
            display: grid;
            gap: 11px;
            list-style: none;
            margin: 22px 0 0;
            padding: 0;
        }

        .check-list li {
            align-items: center;
            display: flex;
            gap: 10px;
        }

        .check-list li::before {
            background: var(--primary);
            border-radius: 999px;
            color: #ffffff;
            content: "\2713";
            display: inline-grid;
            font-size: 12px;
            height: 22px;
            place-items: center;
            width: 22px;
        }

        .mockup,
        .card,
        .team-card,
        .launch-card,
        .gallery-card {
            background: #ffffff;
            border: 1.5px solid var(--line);
            border-radius: 24px;
            box-shadow: var(--shadow);
        }

        .mockup {
            aspect-ratio: 16 / 10;
            display: grid;
            gap: 14px;
            overflow: hidden;
            padding: 20px;
        }

        .mockup-row {
            align-items: center;
            border: 1.5px solid var(--line);
            border-radius: 16px;
            display: grid;
            gap: 12px;
            grid-template-columns: 64px 1fr;
            padding: 12px;
        }

        .mockup-thumb {
            background: linear-gradient(135deg, var(--mint), var(--sage));
            border: 1.5px solid var(--line);
            border-radius: 14px;
            height: 64px;
        }

        .mockup-line {
            background: #e7e8e9;
            border-radius: 999px;
            height: 10px;
            margin: 7px 0;
        }

        .section-head {
            margin: 0 auto 34px;
            max-width: 720px;
            text-align: center;
        }

        .feature-grid,
        .team-grid,
        .launch-grid {
            display: grid;
            gap: 24px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .card,
        .team-card,
        .launch-card {
            padding: 28px;
        }

        .icon {
            background: var(--mint);
            border: 1.5px solid var(--line);
            border-radius: 20px;
            display: grid;
            font-size: 28px;
            height: 62px;
            margin-bottom: 18px;
            place-items: center;
            width: 62px;
        }

        .icon--warm {
            background: var(--peach);
        }

        .icon--sage {
            background: var(--sage);
        }

        h3 {
            font-size: 25px;
            margin: 0 0 8px;
        }

        .card p,
        .team-card p,
        .gallery-card span {
            color: var(--muted);
        }

        .launch {
            background: var(--primary);
            color: #ffffff;
            display: grid;
            gap: 38px;
            grid-template-columns: minmax(0, 0.82fr) minmax(0, 1.18fr);
            padding: clamp(54px, 8vw, 92px) clamp(20px, 5vw, 64px);
        }

        .launch .eyebrow {
            color: var(--mint);
        }

        .launch h2 {
            font-size: clamp(34px, 5vw, 62px);
            line-height: 1;
            max-width: 720px;
        }

        .launch p {
            color: rgba(255, 255, 255, 0.78);
        }

        .launch-grid {
            gap: 16px;
        }

        .launch-card {
            color: var(--ink);
        }

        .launch-card span {
            align-items: center;
            background: var(--mint);
            border: 1.5px solid var(--line);
            border-radius: 999px;
            color: var(--primary);
            display: inline-flex;
            font-weight: 900;
            height: 40px;
            justify-content: center;
            width: 40px;
        }

        .launch-card p {
            color: var(--muted);
            font-size: 14px;
        }

        .gallery {
            display: grid;
            gap: 18px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .gallery-card {
            min-height: 230px;
            overflow: hidden;
            padding: 18px;
        }

        .gallery-card:first-child {
            grid-column: span 2;
        }

        .gallery-art {
            background: linear-gradient(135deg, var(--mint), var(--peach));
            border: 1.5px solid var(--line);
            border-radius: 18px;
            height: 140px;
            margin-bottom: 16px;
        }

        .team-grid {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .team-card {
            text-align: center;
        }

        .avatar {
            align-items: center;
            background: linear-gradient(135deg, var(--primary), #6d8d68);
            border: 1.5px solid var(--line);
            border-radius: 999px;
            color: #ffffff;
            display: inline-flex;
            font-weight: 900;
            height: 78px;
            justify-content: center;
            margin-bottom: 14px;
            width: 78px;
        }

        .footer {
            align-items: start;
            border-top: 1px solid #d1d4d1;
            display: grid;
            gap: 30px;
            grid-template-columns: 1fr auto;
            padding: 48px clamp(20px, 5vw, 64px) 96px;
        }

        .footer h2 {
            color: var(--primary);
            font-size: 28px;
        }

        .footer p {
            color: var(--muted);
            max-width: 430px;
        }

        .footer nav {
            display: grid;
            gap: 14px 54px;
            grid-template-columns: repeat(2, minmax(150px, 1fr));
        }

        @media (max-width: 980px) {
            .hero,
            .split,
            .launch {
                grid-template-columns: 1fr;
            }

            .feature-grid,
            .team-grid,
            .launch-grid,
            .gallery {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 760px) {
            .nav {
                display: none;
            }

            .hero {
                min-height: auto;
                padding-top: 54px;
            }

            .hero-stage {
                min-height: auto;
            }

            .floating-card {
                margin: 18px auto;
                position: relative;
            }

            .floating-card:first-child,
            .floating-card:last-child {
                bottom: auto;
                left: auto;
                right: auto;
                top: auto;
            }

            .feature-grid,
            .team-grid,
            .launch-grid,
            .gallery,
            .footer {
                grid-template-columns: 1fr;
            }

            .gallery-card:first-child {
                grid-column: auto;
            }

            .hero-actions {
                flex-direction: column;
            }

            .hero-actions .button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <a class="brand" href="#">CityZen</a>
        <nav class="nav" aria-label="Landing navigation">
            <a href="#mission">Mission</a>
            <a href="#features">Tools</a>
            <a href="#spaces">Spaces</a>
            <a href="#team">Team</a>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="hero-copy">
                <p class="eyebrow">Crowdsourced public space platform</p>
                <h1>Co-creating <span>sustainable</span> cities.</h1>
                <p>Empowering citizens to map, report, and improve urban spaces through community action and transparent civic data.</p>
                <div class="hero-actions">
                    <a class="button button--primary" href="#features">Explore the Platform</a>
                    <a class="button button--secondary" href="#mission">See the Mission</a>
                </div>
            </div>
            <div class="hero-stage" aria-label="CityZen community preview">
                <article class="floating-card">
                    <span>Fasilkom-TI</span>
                    <strong>4.2</strong>
                    <small>67 reviews · Sustainability score high</small>
                    <div class="meter"><i></i></div>
                </article>
                <article class="floating-card">
                    <span>Community Spotlight</span>
                    <strong>@urban_pioneer</strong>
                    <small>Verified 20 new public spaces this month.</small>
                </article>
            </div>
        </section>

        <section class="split" id="mission">
            <div>
                <p class="eyebrow">Our mission</p>
                <h2>Aligned with SDG 11: Sustainable Cities & Communities</h2>
                <p>CityZen bridges urban data and lived experience so communities can make public spaces more inclusive, safe, resilient, and sustainable.</p>
                <ul class="check-list">
                    <li>Inclusive public space accessibility</li>
                    <li>Real-time environmental and safety reporting</li>
                    <li>Participatory community improvement tools</li>
                </ul>
            </div>
            <div class="mockup" aria-label="CityZen civic activity preview">
                <div class="mockup-row">
                    <div class="mockup-thumb"></div>
                    <div>
                        <div class="mockup-line" style="width: 74%"></div>
                        <div class="mockup-line" style="width: 48%"></div>
                    </div>
                </div>
                <div class="mockup-row">
                    <div class="mockup-thumb"></div>
                    <div>
                        <div class="mockup-line" style="width: 62%"></div>
                        <div class="mockup-line" style="width: 82%"></div>
                    </div>
                </div>
                <div class="mockup-row">
                    <div class="mockup-thumb"></div>
                    <div>
                        <div class="mockup-line" style="width: 84%"></div>
                        <div class="mockup-line" style="width: 36%"></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section" id="features">
            <div class="section-head">
                <p class="eyebrow">Platform tools</p>
                <h2>Empowering Urban Change</h2>
                <p>Tools designed for modern citizens to influence their environment effectively.</p>
            </div>
            <div class="feature-grid">
                <article class="card">
                    <span class="icon">D</span>
                    <h3>Discover</h3>
                    <p>Explore parks, campuses, community spaces, accessibility routes, and public facilities near you.</p>
                </article>
                <article class="card">
                    <span class="icon icon--warm">!</span>
                    <h3>Report</h3>
                    <p>Submit condition reports for damaged facilities, safety concerns, accessibility, or cleanliness issues.</p>
                </article>
                <article class="card">
                    <span class="icon icon--sage">C</span>
                    <h3>Connect</h3>
                    <p>Join local communities, review spaces, and coordinate improvements with other city contributors.</p>
                </article>
            </div>
        </section>

        <section class="launch">
            <div>
                <p class="eyebrow">Now building</p>
                <h2>The first report can change how a city listens.</h2>
                <p>CityZen is starting from zero on purpose: every map pin, review, and report should come from real citizens who care about public spaces.</p>
            </div>
            <div class="launch-grid">
                <article class="launch-card">
                    <span>01</span>
                    <h3>Seed the map</h3>
                    <p>Add the first public places worth protecting, improving, or celebrating.</p>
                </article>
                <article class="launch-card">
                    <span>02</span>
                    <h3>Turn concern into data</h3>
                    <p>Transform scattered complaints into structured civic signals.</p>
                </article>
                <article class="launch-card">
                    <span>03</span>
                    <h3>Build public memory</h3>
                    <p>Create a transparent record of what communities notice and need.</p>
                </article>
            </div>
        </section>

        <section class="section" id="spaces">
            <div class="section-head">
                <p class="eyebrow">Our canvas</p>
                <h2>Transforming Public Spaces</h2>
                <p>A civic interface for mapping needs, surfacing issues, and coordinating local improvements.</p>
            </div>
            <div class="gallery">
                <article class="gallery-card">
                    <div class="gallery-art"></div>
                    <h3>Lapangan Merdeka</h3>
                    <span>Medan, Indonesia</span>
                </article>
                <article class="gallery-card">
                    <div class="gallery-art"></div>
                    <h3>USU Roadwalk</h3>
                    <span>Medan, Indonesia</span>
                </article>
                <article class="gallery-card">
                    <div class="gallery-art"></div>
                    <h3>RingRoad City Walk Park</h3>
                    <span>Public space preview</span>
                </article>
            </div>
        </section>

        <section class="section" id="team">
            <div class="section-head">
                <p class="eyebrow">Team section</p>
                <h2>Built by Students, Designed for Better Cities</h2>
                <p>"Designing the cities we want to live in, one community at a time."</p>
            </div>
            <div class="team-grid">
                <article class="team-card">
                    <span class="avatar">WFS</span>
                    <h3>William Fransisco Sihotang</h3>
                    <p>Project Manager</p>
                </article>
                <article class="team-card">
                    <span class="avatar">AS</span>
                    <h3>Ainuha Suraiya</h3>
                    <p>Frontend Developer</p>
                </article>
                <article class="team-card">
                    <span class="avatar">CR</span>
                    <h3>Chyntia Rachel Anandita Hutabarat</h3>
                    <p>Backend Developer</p>
                </article>
                <article class="team-card">
                    <span class="avatar">FD</span>
                    <h3>Felix Desselo Tambunan</h3>
                    <p>UI/UX Designer</p>
                </article>
                <article class="team-card">
                    <span class="avatar">HN</span>
                    <h3>Hadziq Naufal Sinaga</h3>
                    <p>System & DB Engineer</p>
                </article>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div>
            <h2>CityZen</h2>
            <p>Building the infrastructure for civic participation and sustainable urban development, one neighborhood at a time.</p>
            <small>&copy; 2026 CityZen Civic Tech</small>
        </div>
        <nav>
            <a href="#mission">Sustainability Manifesto</a>
            <a href="#features">Urban Data Privacy</a>
            <a href="#team">Open Source</a>
            <a href="#spaces">Public Spaces</a>
        </nav>
    </footer>
</body>
</html>
