<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CityZen | Co-creating Sustainable Cities</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=hanken-grotesk:700,800|inter:400,500,600,700,800,900"
        rel="stylesheet" />
    <style>
        :root {
            --topbar-height: 64px;
            --surface: #f8f9fa;
            --surface-low: #f0f3f1;
            --ink: #191c1d;
            --muted: #4b5548;
            --line: #c5d2c4;
            --primary: #154212;
            --primary-2: #2d6429;
            --mint: #bcf0ae;
            --sage: #d1e8dd;
            --peach: #ffdbca;
            --shadow: 0 10px 22px rgba(25, 28, 29, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: var(--topbar-height);
        }

        body {
            background: var(--surface);
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, sans-serif;
            margin: 0;
            min-width: 320px;
            overflow-x: hidden;
        }

        body.detail-open {
            overflow: hidden;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .topbar {
            align-items: center;
            animation: topbar-drop 620ms ease both;
            background: rgba(248, 249, 250, 0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--line);
            display: flex;
            gap: 24px;
            height: var(--topbar-height);
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
            transition: letter-spacing 180ms ease, transform 180ms ease;
        }

        .brand:hover,
        .brand:focus-visible {
            letter-spacing: 0.02em;
            outline: none;
            transform: translateY(-1px);
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
            position: relative;
        }

        .nav a::after {
            background: var(--primary-2);
            border-radius: 999px;
            bottom: 2px;
            content: "";
            height: 2px;
            left: 9px;
            position: absolute;
            right: 9px;
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 180ms ease;
        }

        .nav a:hover,
        .nav a:focus-visible {
            background: #eaf4e7;
            color: var(--primary);
        }

        .nav a:hover::after,
        .nav a:focus-visible::after {
            transform: scaleX(1);
        }

        .auth-actions {
            align-items: center;
            display: flex;
            gap: 10px;
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
            position: relative;
            transition: box-shadow 160ms ease, transform 160ms ease, filter 160ms ease;
        }

        .button:hover,
        .button:focus-visible {
            box-shadow: 0 7px 18px rgba(25, 28, 29, 0.1);
            outline: none;
            transform: translateY(-1px);
        }

        .button:active,
        .button.is-pressed {
            box-shadow: 0 3px 10px rgba(25, 28, 29, 0.18);
            filter: saturate(1.08);
            transform: translateY(2px) scale(0.97);
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
            background: linear-gradient(180deg, rgba(188, 240, 174, 0.18), rgba(248, 249, 250, 1));
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(300px, 0.8fr);
            isolation: isolate;
            min-height: calc(100vh - 64px);
            overflow: hidden;
            padding: clamp(64px, 9vw, 116px) clamp(20px, 5vw, 64px);
            position: relative;
        }

        .hero::before {
            animation: grid-drift 16s linear infinite;
            background-image:
                linear-gradient(rgba(21, 66, 18, 0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(21, 66, 18, 0.08) 1px, transparent 1px);
            background-size: 42px 42px;
            content: "";
            inset: 0;
            mask-image: linear-gradient(90deg, rgba(0, 0, 0, 0.72), rgba(0, 0, 0, 0.1));
            pointer-events: none;
            position: absolute;
            z-index: -1;
        }

        .hero::after {
            animation: hero-sweep 900ms ease 180ms both;
            background: linear-gradient(110deg, transparent 0%, rgba(255, 255, 255, 0.52) 46%, transparent 70%);
            content: "";
            height: 140%;
            left: -46%;
            pointer-events: none;
            position: absolute;
            top: -20%;
            transform: translateX(-40%) rotate(8deg);
            width: 42%;
            z-index: 0;
            opacity: 0.45;
        }

        .hero-copy {
            max-width: 760px;
            position: relative;
            z-index: 1;
        }

        .hero-copy>* {
            animation: hero-rise 760ms cubic-bezier(0.2, 0.8, 0.2, 1) both;
        }

        .hero-copy>.eyebrow {
            animation-delay: 80ms;
        }

        .hero-copy>h1 {
            animation-delay: 170ms;
        }

        .hero-copy>p:not(.eyebrow) {
            animation-delay: 270ms;
        }

        .hero-actions {
            animation-delay: 380ms;
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
            background-image: linear-gradient(90deg, rgba(188, 240, 174, 0.66), rgba(255, 219, 202, 0.62), rgba(188, 240, 174, 0.66));
            background-size: 220% 100%;
            border-radius: 12px;
            color: var(--primary);
            display: inline-block;
            padding: 0 10px 6px;
            animation: highlight-roll 3.2s ease-in-out 900ms infinite;
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
            animation: hero-stage-in 780ms cubic-bezier(0.2, 0.8, 0.2, 1) 260ms both;
            min-height: 470px;
            position: relative;
            z-index: 1;
        }

        .floating-card {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: var(--shadow);
            display: grid;
            gap: 10px;
            padding: 20px;
            position: absolute;
            transform: rotate(var(--tilt));
            width: min(280px, 80vw);
        }

        .floating-card:first-child {
            --tilt: -3deg;
            animation: card-pop 720ms ease 420ms both, float-card 5.8s ease-in-out 1200ms infinite;
            left: 0;
            top: 18%;
        }

        .floating-card:last-child {
            --tilt: 5deg;
            animation: card-pop 720ms ease 560ms both, float-card 6.4s ease-in-out 1320ms infinite reverse;
            background: var(--mint);
            bottom: 18%;
            right: 0;
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
            animation: meter-fill 1200ms cubic-bezier(0.2, 0.8, 0.2, 1) 900ms both;
            background: var(--primary);
            display: block;
            height: 100%;
            transform-origin: left;
            width: 84%;
        }

        body.anim-ready .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition:
                opacity 620ms ease var(--reveal-delay, 0ms),
                transform 620ms cubic-bezier(0.2, 0.8, 0.2, 1) var(--reveal-delay, 0ms);
        }

        body.anim-ready .reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        .split,
        .section {
            margin: 0 auto;
            max-width: 1280px;
            min-height: calc(100svh - var(--topbar-height));
            padding: clamp(58px, 8vw, 88px) clamp(20px, 5vw, 64px);
            scroll-margin-top: var(--topbar-height);
        }

        .section {
            align-content: center;
            display: grid;
        }

        .split {
            align-items: center;
            background: var(--surface-low);
            display: grid;
            gap: 38px;
            grid-template-columns: minmax(0, 1fr) minmax(320px, 0.9fr);
            max-width: none;
        }

        .split>* {
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
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: var(--shadow);
        }

        .mockup {
            aspect-ratio: 16 / 10;
            display: block;
            background-color: #ffffff;
            padding: 16px;
            border-radius: 16px;
            box-shadow: var(--shadow);
            position: relative;
        }

        .mockup img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            border-radius: 12px;
        }

        .mockup::after {
            animation: panel-scan 3.4s ease-in-out infinite;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.72), transparent);
            content: "";
            inset: 0 auto 0 -40%;
            pointer-events: none;
            position: absolute;
            width: 34%;
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

        .feature-grid .card:nth-child(2),
        .launch-grid .launch-card:nth-child(2),
        .gallery .gallery-card:nth-child(2),
        .team-grid .team-card:nth-child(2) {
            --reveal-delay: 90ms;
        }

        .feature-grid .card:nth-child(3),
        .launch-grid .launch-card:nth-child(3),
        .gallery .gallery-card:nth-child(3),
        .team-grid .team-card:nth-child(3) {
            --reveal-delay: 180ms;
        }

        .team-grid .team-card:nth-child(4) {
            --reveal-delay: 270ms;
        }

        .team-grid .team-card:nth-child(5) {
            --reveal-delay: 360ms;
        }

        .card,
        .launch-card,
        .gallery-card {
            transition: box-shadow 180ms ease, transform 180ms ease;
        }

        .card:hover,
        .card:focus-within {
            box-shadow: 8px 8px 0 rgba(25, 28, 29, 0.16);
            transform: translateY(-7px) rotate(-0.7deg);
        }

        .feature-grid .card:nth-child(2):hover,
        .feature-grid .card:nth-child(2):focus-within {
            transform: translateY(-7px) rotate(0.7deg);
        }

        .launch-card:hover {
            box-shadow: 7px 7px 0 rgba(188, 240, 174, 0.34);
            transform: translateY(-6px);
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
            align-items: center;
            background: var(--primary);
            color: #ffffff;
            display: grid;
            gap: 38px;
            grid-template-columns: minmax(0, 0.82fr) minmax(0, 1.18fr);
            min-height: calc(100svh - var(--topbar-height));
            padding: clamp(54px, 8vw, 92px) clamp(20px, 5vw, 64px);
            scroll-margin-top: var(--topbar-height);
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
            position: relative;
        }

        .gallery-card::after {
            background: linear-gradient(100deg, transparent, rgba(255, 255, 255, 0.62), transparent);
            content: "";
            inset: 0 auto 0 -55%;
            pointer-events: none;
            position: absolute;
            transition: transform 520ms ease;
            width: 42%;
        }

        .gallery-card:hover {
            box-shadow: 8px 8px 0 rgba(25, 28, 29, 0.15);
            transform: translateY(-5px);
        }

        .gallery-card:hover::after {
            transform: translateX(360%);
        }

        .gallery-card:first-child {
            grid-column: span 2;
        }

        .gallery-art {
            animation: art-pulse 5.2s ease-in-out infinite;
            background: linear-gradient(135deg, var(--mint), var(--peach));
            background-size: 180% 180%;
            border: 1.5px solid var(--line);
            border-radius: 18px;
            height: 140px;
            margin-bottom: 16px;
            overflow: hidden;
        }

        .gallery-art img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .team-grid {
            align-items: stretch;
            gap: clamp(16px, 2vw, 24px);
            grid-template-columns: repeat(5, minmax(170px, 1fr));
        }

        .team-card {
            align-content: start;
            cursor: pointer;
            display: grid;
            gap: 13px;
            isolation: isolate;
            justify-items: center;
            min-height: 298px;
            overflow: hidden;
            padding: 30px 22px 26px;
            position: relative;
            text-align: center;
            transition:
                background-color 180ms ease,
                box-shadow 180ms ease,
                transform 180ms ease;
        }

        .team-card::before {
            background: linear-gradient(135deg, rgba(188, 240, 174, 0.78), rgba(209, 232, 221, 0.62));
            border-bottom: 1.5px solid rgba(25, 28, 29, 0.16);
            content: "";
            height: 76px;
            inset: 0 0 auto;
            position: absolute;
            transform: translateY(-100%);
            transition: transform 200ms ease;
            z-index: -1;
        }

        .team-card:hover,
        .team-card:focus-visible {
            background: #fcfffb;
            box-shadow: 9px 9px 0 rgba(25, 28, 29, 0.14);
            outline: none;
            transform: translate(-3px, -3px);
        }

        .team-card:hover::before,
        .team-card:focus-visible::before,
        .team-card.is-pressed::before {
            transform: translateY(0);
        }

        .team-card:active,
        .team-card.is-pressed {
            box-shadow: 4px 4px 0 rgba(25, 28, 29, 0.16);
            transform: translate(4px, 4px) scale(0.985);
        }

        .team-card.is-pressed[data-member-card] {
            transform: translate(4px, 4px) scale(0.94);
        }

        .team-card.is-pressed .avatar {
            animation: avatar-pop 420ms ease;
        }

        .team-card h3 {
            font-size: clamp(22px, 2.1vw, 29px);
            line-height: 1.12;
            margin: 4px 0 0;
            max-width: 210px;
        }

        .team-card p {
            background: var(--surface-low);
            border: 1px solid rgba(25, 28, 29, 0.12);
            border-radius: 999px;
            color: #334331;
            font-size: 15px;
            font-weight: 800;
            line-height: 1.25;
            margin: auto 0 0;
            max-width: 100%;
            padding: 8px 13px;
        }

        .avatar {
            align-items: center;
            background: linear-gradient(135deg, var(--primary), #6d8d68);
            border: 1.5px solid var(--line);
            border-radius: 999px;
            color: #ffffff;
            display: inline-flex;
            font-weight: 900;
            height: 86px;
            justify-content: center;
            margin-bottom: 4px;
            position: relative;
            transition: transform 180ms ease;
            width: 86px;
        }

        .avatar::after {
            border: 1.5px solid rgba(25, 28, 29, 0.16);
            border-radius: inherit;
            content: "";
            inset: -7px;
            opacity: 0;
            position: absolute;
            transform: scale(0.72);
            transition: opacity 180ms ease, transform 180ms ease;
        }

        .team-card:hover .avatar,
        .team-card:focus-visible .avatar {
            transform: translateY(-2px) scale(1.03);
        }

        .team-card:hover .avatar::after,
        .team-card:focus-visible .avatar::after,
        .team-card.is-pressed .avatar::after {
            opacity: 1;
            transform: scale(1);
        }

        .member-detail {
            align-items: center;
            background:
                linear-gradient(135deg, rgba(21, 66, 18, 0.18), rgba(255, 219, 202, 0.16)),
                rgba(10, 16, 12, 0.58);
            display: grid;
            inset: 0;
            opacity: 0;
            padding: 24px;
            pointer-events: none;
            position: fixed;
            transition: opacity 220ms ease, visibility 220ms ease;
            visibility: hidden;
            z-index: 70;
        }

        .member-detail.is-open {
            opacity: 1;
            pointer-events: auto;
            visibility: visible;
        }

        .member-detail__card {
            background: #ffffff;
            border: 1.5px solid var(--line);
            border-radius: 24px;
            box-shadow: 18px 18px 0 rgba(25, 28, 29, 0.18);
            margin: auto;
            max-height: calc(100vh - 36px);
            overflow: hidden;
            position: relative;
            transform: translateY(26px) scale(0.88);
            transition:
                box-shadow 220ms ease,
                transform 260ms cubic-bezier(0.2, 0.8, 0.2, 1);
            width: min(980px, 100%);
        }

        .member-detail.is-open .member-detail__card {
            box-shadow: 10px 10px 0 rgba(25, 28, 29, 0.2);
            transform: translateY(0) scale(1);
        }

        .member-detail__card.is-head-only {
            width: min(720px, 100%);
        }

        .member-detail__hero {
            background: linear-gradient(135deg, var(--mint), var(--sage) 54%, var(--peach));
            border-bottom: 1.5px solid var(--line);
            display: grid;
            gap: 18px;
            grid-template-columns: auto 1fr auto;
            padding: clamp(18px, 3vw, 28px);
        }

        .member-detail__avatar {
            align-items: center;
            background: var(--primary);
            border: 1.5px solid var(--line);
            border-radius: 28px;
            color: #ffffff;
            display: inline-flex;
            font-size: 22px;
            font-weight: 900;
            height: 78px;
            justify-content: center;
            transform: rotate(-4deg);
            width: 78px;
        }

        .member-detail__hero h2 {
            font-size: clamp(28px, 4vw, 42px);
            margin: 0 0 6px;
        }

        .member-detail__hero p {
            color: #253326;
            font-size: 15px;
            line-height: 1.45;
            margin: 0;
            max-width: 720px;
        }

        .member-detail__close {
            align-items: center;
            background: #ffffff;
            border: 1.5px solid var(--line);
            border-radius: 999px;
            cursor: pointer;
            display: inline-flex;
            font-size: 24px;
            font-weight: 900;
            height: 42px;
            justify-content: center;
            line-height: 1;
            transition: background-color 160ms ease, transform 160ms ease;
            width: 42px;
        }

        .member-detail__close:hover,
        .member-detail__close:focus-visible {
            background: var(--primary);
            color: #ffffff;
            outline: none;
            transform: rotate(8deg) scale(1.04);
        }

        .member-detail__body {
            display: grid;
            gap: 14px;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            padding: clamp(16px, 2.8vw, 24px);
        }

        .member-detail__body.is-empty {
            display: none;
        }

        .member-detail__panel {
            background: var(--surface);
            border: 1.5px solid var(--line);
            border-radius: 18px;
            padding: 16px;
        }

        .member-detail__panel--blank {
            min-height: 118px;
        }

        .member-detail__field--empty {
            background:
                linear-gradient(90deg, rgba(21, 66, 18, 0.08), rgba(255, 255, 255, 0.82), rgba(21, 66, 18, 0.08));
            border: 1px dashed rgba(25, 28, 29, 0.18);
            border-radius: 12px;
            min-height: 48px;
        }

        .member-detail__panel h3 {
            font-size: 19px;
            margin: 0 0 9px;
        }

        .member-detail__panel p {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.48;
            margin: 0;
        }

        .member-detail__list {
            display: grid;
            gap: 7px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .member-detail__list li {
            align-items: start;
            color: var(--muted);
            display: grid;
            font-size: 14px;
            gap: 8px;
            grid-template-columns: 24px 1fr;
            line-height: 1.35;
        }

        .member-detail__list li::before {
            background: var(--primary);
            border-radius: 999px;
            color: #ffffff;
            content: "\2713";
            display: inline-grid;
            font-size: 12px;
            height: 22px;
            margin-top: 2px;
            place-items: center;
            width: 22px;
        }

        .member-detail__list li:empty {
            min-height: 24px;
        }

        .member-detail__list li:empty::after {
            align-self: center;
            background:
                linear-gradient(90deg, rgba(21, 66, 18, 0.08), rgba(255, 255, 255, 0.82), rgba(21, 66, 18, 0.08));
            border: 1px dashed rgba(25, 28, 29, 0.16);
            border-radius: 999px;
            content: "";
            height: 13px;
            width: 100%;
        }

        .member-detail__pills {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
        }

        .member-detail__pill {
            background: #ffffff;
            border: 1px solid rgba(25, 28, 29, 0.16);
            border-radius: 999px;
            color: #334331;
            font-size: 12px;
            font-weight: 800;
            padding: 7px 10px;
        }

        .member-detail__pill--empty {
            min-height: 30px;
            min-width: 92px;
        }

        .member-detail__stats {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .member-detail__stat {
            background: #ffffff;
            border: 1.5px solid var(--line);
            border-radius: 16px;
            padding: 12px;
        }

        .member-detail__stat strong {
            color: var(--primary);
            display: block;
            font-family: "Hanken Grotesk", Inter, sans-serif;
            font-size: 22px;
            line-height: 1;
            margin-bottom: 5px;
        }

        .member-detail__stat strong:empty,
        .member-detail__stat span:empty {
            min-height: 18px;
        }

        .member-detail__stat strong:empty::after,
        .member-detail__stat span:empty::after {
            background:
                linear-gradient(90deg, rgba(21, 66, 18, 0.08), rgba(255, 255, 255, 0.82), rgba(21, 66, 18, 0.08));
            border: 1px dashed rgba(25, 28, 29, 0.16);
            border-radius: 999px;
            content: "";
            display: block;
            height: 13px;
            width: 100%;
        }

        .member-detail__stat strong:empty::after {
            height: 17px;
            width: 72%;
        }

        .member-detail__stat span {
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
        }

        @keyframes avatar-pop {
            0% {
                transform: scale(1);
            }

            46% {
                transform: scale(1.14) rotate(-2deg);
            }

            100% {
                transform: scale(1);
            }
        }

        @keyframes topbar-drop {
            from {
                opacity: 0;
                transform: translateY(-18px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes hero-rise {
            from {
                opacity: 0;
                transform: translateY(24px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes hero-stage-in {
            from {
                opacity: 0;
                transform: translateX(28px) scale(0.96);
            }

            to {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        @keyframes card-pop {
            from {
                opacity: 0;
                transform: translateY(28px) rotate(var(--tilt)) scale(0.9);
            }

            to {
                opacity: 1;
                transform: translateY(0) rotate(var(--tilt)) scale(1);
            }
        }

        @keyframes float-card {

            0%,
            100% {
                transform: translateY(0) rotate(var(--tilt));
            }

            50% {
                transform: translateY(-18px) rotate(calc(var(--tilt) + 1.8deg));
            }
        }

        @keyframes meter-fill {
            from {
                transform: scaleX(0);
            }

            to {
                transform: scaleX(1);
            }
        }

        @keyframes highlight-roll {

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        @keyframes grid-drift {
            from {
                background-position: 0 0, 0 0;
            }

            to {
                background-position: 42px 42px, 42px 42px;
            }
        }

        @keyframes hero-sweep {
            from {
                transform: translateX(-40%) rotate(8deg);
            }

            to {
                transform: translateX(430%) rotate(8deg);
            }
        }

        @keyframes panel-scan {

            0%,
            42% {
                transform: translateX(0);
            }

            74%,
            100% {
                transform: translateX(420%);
            }
        }

        @keyframes art-pulse {

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
            }

            .button,
            .team-card,
            .team-card::before,
            .card,
            .launch-card,
            .gallery-card,
            .avatar,
            .avatar::after,
            .member-detail,
            .member-detail__card,
            .member-detail__close {
                transition: none;
            }

            .team-card.is-pressed .avatar {
                animation: none;
            }
        }

        @supports not (height: 100svh) {

            .split,
            .section,
            .launch {
                min-height: calc(100vh - var(--topbar-height));
            }
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

        /* Visual polish: keep the same structure, make the surface calmer and less template-like. */
        .topbar {
            background: rgba(247, 249, 244, 0.96);
            border-bottom-color: rgba(31, 44, 33, 0.2);
            box-shadow: 0 1px 0 rgba(31, 44, 33, 0.04);
        }

        .brand {
            letter-spacing: -0.02em;
        }

        .nav a {
            color: #344337;
            transition: color 160ms ease, background 160ms ease;
        }

        .nav a:hover,
        .nav a:focus-visible {
            background: rgba(213, 232, 205, 0.62);
            color: var(--primary);
            outline: none;
        }

        .hero {
            background: #f7f9f3;
        }

        .hero::before {
            animation: none;
            opacity: 0.16;
        }

        .hero::after {
            display: none;
        }

        .hero-copy>*,
        .hero-stage {
            animation-duration: 520ms;
        }

        .hero-copy>h1 {
            letter-spacing: -0.04em;
        }

        .hero p:not(.eyebrow),
        .section-head p,
        .split p,
        .launch p {
            color: #536052;
        }

        .button {
            border-width: 1px;
            box-shadow: none;
            letter-spacing: 0;
            min-height: 42px;
            transition: background 160ms ease, color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
        }

        .button:hover,
        .button:focus-visible {
            box-shadow: 0 6px 14px rgba(25, 28, 29, 0.08);
            transform: translateY(-1px);
        }

        .button:active,
        .button.is-pressed {
            box-shadow: 0 2px 8px rgba(25, 28, 29, 0.12);
            transform: translateY(1px);
        }

        .floating-card,
        .mockup,
        .card,
        .team-card,
        .launch-card,
        .gallery-card,
        .member-detail__card {
            border-width: 1px;
            border-radius: 12px;
            box-shadow: 3px 4px 0 rgba(25, 28, 29, 0.06);
        }

        .floating-card:first-child,
        .floating-card:last-child {
            animation: card-pop 520ms ease both;
        }

        .floating-card {
            background: rgba(255, 255, 251, 0.96);
        }

        .mockup {
            background: #fbfcf7;
        }

        .mockup::before {
            box-shadow: 0 0 0 1px rgba(25, 28, 29, 0.08);
        }

        .card,
        .launch-card,
        .gallery-card {
            background: #fffef9;
        }

        .card:hover,
        .launch-card:hover,
        .gallery-card:hover {
            box-shadow: 4px 5px 0 rgba(25, 28, 29, 0.07);
            transform: translateY(-2px);
        }

        .gallery-card::after {
            display: none;
        }

        .team-card {
            background: #fffef9;
            padding: 26px;
            transition: background 160ms ease, box-shadow 160ms ease, transform 160ms ease;
        }

        .team-card::before {
            background: rgba(223, 236, 218, 0.74);
            border-bottom-color: rgba(25, 28, 29, 0.12);
        }

        .team-card:hover,
        .team-card:focus-visible {
            background: #fffffb;
            box-shadow: 4px 5px 0 rgba(25, 28, 29, 0.07);
            transform: translateY(-2px);
        }

        .team-card:active,
        .team-card.is-pressed,
        .team-card.is-pressed[data-member-card] {
            box-shadow: 2px 3px 0 rgba(25, 28, 29, 0.12);
            transform: translateY(1px) scale(0.99);
        }

        .team-card p {
            background: rgba(238, 244, 240, 0.78);
            border-color: rgba(25, 28, 29, 0.08);
        }

        .avatar,
        .member-detail__avatar {
            background: #386e3b;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.16);
        }

        .avatar::after {
            display: none;
        }

        .member-detail__hero {
            background: #e7f2df;
            border-bottom-width: 1px;
        }

        .member-detail__close {
            box-shadow: none;
        }

        .footer {
            background: #f3f6ef;
            border-top-color: rgba(31, 44, 33, 0.14);
        }

        .section {
            padding-block: clamp(54px, 8vw, 92px);
        }

        .section-head {
            margin-bottom: 28px;
        }

        .eyebrow {
            letter-spacing: 0.11em;
        }

        .card h3,
        .team-card h3,
        .launch-card strong {
            letter-spacing: -0.01em;
        }

        .card p,
        .team-card p,
        .launch-card p {
            color: #566153;
            line-height: 1.54;
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

            .auth-actions {
                gap: 8px;
            }

            .auth-actions .button {
                min-height: 38px;
                padding: 8px 12px;
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
            .member-detail__body,
            .footer {
                grid-template-columns: 1fr;
            }

            .member-detail {
                padding: 14px;
            }

            .member-detail__card {
                max-height: calc(100vh - 28px);
                overflow: auto;
            }

            .member-detail__hero {
                grid-template-columns: 1fr auto;
            }

            .member-detail__avatar {
                grid-row: 1 / 3;
            }

            .member-detail__hero>div {
                grid-column: 1 / -1;
            }

            .member-detail__stats {
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
        <div class="auth-actions" aria-label="Authentication actions">
            <a class="button button--secondary" href="{{ url('/login') }}">Login</a>
            <a class="button button--primary" href="{{ url('/register') }}">Register</a>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="hero-copy">
                <p class="eyebrow">Crowdsourced public space platform</p>
                <h1>Co-creating <span>sustainable</span> cities.</h1>
                <p>Empowering citizens to map, report, and improve urban spaces through community action and transparent
                    civic data.</p>
                <div class="hero-actions">
                    <a class="button button--primary" href="{{ url('/register') }}">Join the Movement</a>
                    <a class="button button--secondary" href="{{ url('/login') }}">Login</a>
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

        <section class="split reveal" id="mission" data-reveal>
            <div class="reveal" data-reveal>
                <p class="eyebrow">Our mission</p>
                <h2>Aligned with SDG 11: Sustainable Cities & Communities</h2>
                <p>CityZen bridges urban data and lived experience so communities can make public spaces more inclusive,
                    safe, resilient, and sustainable.</p>
                <ul class="check-list">
                    <li>Inclusive public space accessibility</li>
                    <li>Real-time environmental and safety reporting</li>
                    <li>Participatory community improvement tools</li>
                </ul>
            </div>
            <div class="mockup reveal" data-reveal aria-label="CityZen civic activity preview">
                <img src="/photo/foto(1).png">
            </div>
        </section>

        <section class="section" id="features">
            <div class="section-head reveal" data-reveal>
                <p class="eyebrow">Platform tools</p>
                <h2>Empowering Urban Change</h2>
                <p>Tools designed for modern citizens to influence their environment effectively.</p>
            </div>
            <div class="feature-grid">
                <article class="card reveal" data-reveal>
                    <span class="icon">D</span>
                    <h3>Discover</h3>
                    <p>Explore parks, campuses, community spaces, accessibility routes, and public facilities near you.
                    </p>
                </article>
                <article class="card reveal" data-reveal>
                    <span class="icon icon--warm">!</span>
                    <h3>Report</h3>
                    <p>Submit condition reports for damaged facilities, safety concerns, accessibility, or cleanliness
                        issues.</p>
                </article>
                <article class="card reveal" data-reveal>
                    <span class="icon icon--sage">C</span>
                    <h3>Connect</h3>
                    <p>Join local communities, review spaces, and coordinate improvements with other city contributors.
                    </p>
                </article>
            </div>
        </section>

        <section class="launch reveal" data-reveal>
            <div class="reveal" data-reveal>
                <p class="eyebrow">Now building</p>
                <h2>The first report can change how a city listens.</h2>
                <p>CityZen is starting from zero on purpose: every map pin, review, and report should come from real
                    citizens who care about public spaces.</p>
            </div>
            <div class="launch-grid">
                <article class="launch-card reveal" data-reveal>
                    <span>01</span>
                    <h3>Seed the map</h3>
                    <p>Add the first public places worth protecting, improving, or celebrating.</p>
                </article>
                <article class="launch-card reveal" data-reveal>
                    <span>02</span>
                    <h3>Turn concern into data</h3>
                    <p>Transform scattered complaints into structured civic signals.</p>
                </article>
                <article class="launch-card reveal" data-reveal>
                    <span>03</span>
                    <h3>Build public memory</h3>
                    <p>Create a transparent record of what communities notice and need.</p>
                </article>
            </div>
        </section>

        <section class="section" id="spaces">
            <div class="section-head reveal" data-reveal>
                <p class="eyebrow">Our canvas</p>
                <h2>Transforming Public Spaces</h2>
                <p>A civic interface for mapping needs, surfacing issues, and coordinating local improvements.</p>
            </div>
            <div class="gallery">
                <article class="gallery-card reveal" data-reveal>
                    <div class="gallery-art">
                        <img src="https://awsimages.detik.net.id/community/media/visual/2025/02/20/wajah-baru-lapangan-merdeka-medan-1740021678016_169.jpeg?w=700&q=90" alt="Lapangan Merdeka Medan">
                    </div>
                        <h3>Lapangan Merdeka</h3>
                        <span>Medan, Indonesia</span>
                </article>
                <article class="gallery-card reveal" data-reveal>
                    <div class="gallery-art">
                        <img src="https://1.bp.blogspot.com/-ZkHd5hWDpVM/XQ-Dk8Fn-II/AAAAAAAAAKs/qtuaGvylc0UlpZ3T_aR2g_hZHueQIfr5QCLcBGAs/s1600/IMG-20190308-WA0010.jpg" alt="">
                    </div>
                    <h3>USU Roadwalk</h3>
                    <span>Medan, Indonesia</span>
                </article>
                <article class="gallery-card reveal" data-reveal>
                    <div class="gallery-art">
                        <img src="https://tse1.mm.bing.net/th/id/OIP.YAhTDbDWYDCq486suxLJaQHaE8?cb=thfc1falcon&rs=1&pid=ImgDetMain&o=7&rm=3" alt="RingRoad City Walk Park">
                    </div>
                    <h3>RingRoad City Walk Park</h3>
                    <span>Public space preview</span>
                </article>
            </div>
        </section>

        <section class="section" id="team">
            <div class="section-head reveal" data-reveal>
                <p class="eyebrow">Team section</p>
                <h2>Built by Students, Designed for Better Cities</h2>
                <p>"Designing the cities we want to live in, one community at a time."</p>
            </div>
            <div class="team-grid">
                <article class="team-card reveal" tabindex="0" role="button" data-reveal data-team-card
                    data-member-card="project-manager" data-member-avatar="WFS"
                    data-member-photo="{{ asset('photo/pfpPM.png') }}"
                    data-member-name="William Fransisco Sihotang" data-member-role="Project Manager"
                    data-member-summary="Mahasiswa yang suka mengubah ide acak menjadi rencana yang rapi, senang berdiskusi, dan punya ketertarikan besar pada presentasi, visual, serta koordinasi tim."
                    aria-controls="member-detail-profile" aria-expanded="false" aria-haspopup="dialog"
                    aria-label="Buka detail William Fransisco Sihotang, Project Manager">
                    <img src="{{ asset('photo/pfpPM.png') }}" alt="William Fransisco Sihotang" class="avatar" style="object-fit: cover;">
                    <h3>William Fransisco Sihotang</h3>
                    <p>Project Manager</p>
                </article>
                <article class="team-card reveal" tabindex="0" role="button" data-reveal data-team-card
                    data-member-card="ainuha-suraiya"
                    data-member-photo="{{ asset('photo/Ainuha.jpeg') }}"
                    data-member-name="Ainuha Suraiya"
                    data-member-role="Frontend Developer"
                    data-member-summary="Mahasiswa dengan minat pada tampilan web yang bersih, responsif, dan nyaman dipakai."
                    data-member-about="Tipe orang yang senang mengubah ide menjadi tampilan yang bisa dilihat dan digunakan langsung. Punya ketertarikan pada desain, interaksi pengguna, dan bagaimana sebuah website dapat terasa nyaman saat digunakan.
                        Di tim, bertugas membangun tampilan CityZen agar responsif, intuitif, dan mudah dipahami. Percaya bahwa pengalaman pengguna yang baik dapat membuat sebuah sistem menjadi lebih bermanfaat."
                    data-member-interests="HTML|CSS|JavaScript|UI/UX Design|Graphic Design|Canva"
                    data-member-personality="Suka mempelajari hal baru, terutama di bidang IT, bisnis, dan pengembangan digital.| Senang menggabungkan logika, kreativitas, dan pemecahan masalah dalam sebuah proyek.|
                        Berorientasi pada pembelajaran dan pengembangan diri secara berkelanjutan.| Mudah beradaptasi dengan tantangan dan teknologi baru."
                    data-member-facts="Domain: IT, Business & Digital Innovation.| Specialty: Frontend Development. | Goal: One step closer to mastery every day.| Status: Building, learning, and growing..."
                    aria-controls="member-detail-profile" aria-expanded="false" aria-haspopup="dialog"
                    aria-label="Buka detail Ainuha Suraiya, Frontend Developer">

                    <img src="{{ asset('photo/Ainuha.jpeg') }}" alt="Ainuha Suraiya" class="avatar" style="object-fit: cover;">
                    <h3>Ainuha Suraiya</h3>
                    <p>Frontend Developer</p>
                </article>
                <article class="team-card reveal" tabindex="0" role="button" data-reveal data-team-card data-member-card="Chyntia Hutabarat" data-member-avatar="CH" data-member-name="Chyntia Rachel Anandita Hutabarat" data-member-role="BackendDeveloper" data-member-summary="Mahasiswa semester 2 yang tertarik mengeksplorasi kode web." data-member-about="Mahasiswa semester 2 yang tertarik dengan perpaduan antara logika pemrograman dan estetika visual. Sangat menikmati proses menerjemahkan ide menjadi struktur kode yang simpel dan mudah dimengerti, serta merancang visual mockup agar presentasi proyek terlihat lebih menarik."
                        data-member-card="Chyntia-Hutabarat" data-member-photo="{{ asset('photo/chyntia.jpg') }}"
                        data-member-interests="SQL|Graphic Design|Time Management|Workout|Team Work|Canva"
                        data-member-personality="Suka membuat jadwal terstruktur untuk menyeimbangkan tugas kuliah, eksplorasi kode, dan latihan fisik mingguan.|Lebih menyukai pendekatan visual seperti animasi 2D yang simpel namun informatif dibandingkan desain realistis yang rumit.|Fleksibel dalam merencanakan pertemuan."
                        data-member-facts="Style: Simpel & Terstruktur|Mode: Terjadwal|Habit: Olahraga|Goal: Ahli Database"
                        aria-controls="member-detail-profile" aria-expanded="false" aria-haspopup="dialog">

                        <img src="{{ asset('photo/chyntia.jpg') }}" alt="Chyntia Rachel Anandita Hutabarat" class="avatar" style="object-fit: cover;">

                    <h3>Chyntia Rachel Anandita Hutabarat</h3>
                    <p>Backend Developer</p>
                </article>

                <article class="team-card reveal" tabindex="0" role="button" data-reveal data-team-card
                    data-member-card="felix-desselo" data-member-photo="{{ asset('photo/Felix.jpeg') }}"
                    data-member-name="Felix Desselo Tambunan" data-member-role="UI/UX Designer"
                    data-member-summary="Mahasiswa yang fokus merancang tampilan, alur pengguna, dan pengalaman visual agar CityZen terasa jelas, nyaman, dan mudah digunakan."
                    data-member-about="Berperan dalam merancang pengalaman pengguna CityZen mulai dari struktur halaman, alur interaksi, hingga detail visual. Fokus pada tampilan yang rapi, mudah dipahami, dan mendukung kebutuhan pengguna saat melaporkan masalah ruang publik. Senang mengubah ide awal menjadi desain yang lebih terarah dan siap dikembangkan."
                    data-member-interests="UI Design|UX Flow|Wireframe|Visual Hierarchy|Canva|Figma"
                    data-member-personality="Suka memperhatikan detail tampilan dan kerapian layout.|Senang mengeksplorasi warna, komposisi, dan visual hierarchy.|Berusaha membuat desain yang sederhana namun tetap informatif.|Terbuka dengan masukan agar desain lebih nyaman digunakan."
                    data-member-facts="Role: UI/UX Designer|Focus: User Flow &amp; Visual Design|Strength: Layout &amp; Presentation|Goal: Make CityZen easy to use"
                    aria-controls="member-detail-profile" aria-expanded="false" aria-haspopup="dialog"
                    aria-label="Buka detail Felix Desselo Tambunan, UI/UX Designer">
                    <img src="{{ asset('photo/Felix.jpeg') }}" alt="Felix Desselo Tambunan" class="avatar" style="object-fit: cover;">

                    <h3>Felix Desselo Tambunan</h3>
                    <p>UI/UX Designer</p>
                </article>

                <article class="team-card reveal" tabindex="0" role="button" data-reveal data-team-card
                    data-member-card="Hadziq-Naufal" data-member-photo="{{ asset('photo/Naufal.jpeg') }}"
                    data-member-name="Hadziq Naufal Sinaga" data-member-role="System &amp; DB Engineer"
                    data-member-summary="Mahasiswa yang fokus pada struktur database, koneksi sistem, dan kerapian data."
                    data-member-about="Tipe orang yang punya rasa ingin tahu yang tinggi sama cara kerja sistem di balik layar. Di tim, perannya mastiin pondasi data tetap rapi dan logikanya jalan. Percaya kalau kode yang error selalu bisa diperbaiki, apalagi kalau udah refreshing sebentar."
                    data-member-interests="Database Design|Laravel|SQL|C++|PHP|Canva"
                    data-member-personality="Teliti dalam merancang ERD dan relasi tabel.|Suka ngoding sambil dengerin lagu santai.|Senang diskusi bareng teman kampus buat nyari solusi pas lagi stuck ngoding."
                    data-member-facts="Domain: Backend & System|Specialty: Database Normalization|Daily Driver: Axioo Hype 5|Status: Debugging code..."
                    aria-controls="member-detail-profile" aria-expanded="false" aria-haspopup="dialog"
                    aria-label="Buka detail Hadziq Naufal Sinaga, System &amp; DB Engineer">

                    <img src="{{ asset('photo/Naufal.jpeg') }}" alt="Hadziq Naufal Sinaga" class="avatar"
                        style="object-fit: cover;">

                    <h3>Hadziq Naufal Sinaga</h3>
                    <p>System & DB Engineer</p>
                </article>
            </div>
        </section>
    </main>

    <div class="member-detail" id="member-detail-profile" role="dialog" aria-modal="true" aria-hidden="true"
        aria-labelledby="member-detail-title" data-member-detail inert>
        <article class="member-detail__card" role="document">
            <header class="member-detail__hero">
                <span class="member-detail__avatar" aria-hidden="true" data-member-detail-avatar>WFS</span>
                <div>
                    <p class="eyebrow" data-member-detail-role>Project Manager</p>
                    <h2 id="member-detail-title" data-member-detail-name>William Fransisco Sihotang</h2>
                    <p data-member-detail-summary>Mahasiswa yang suka mengubah ide acak menjadi rencana yang rapi,
                        senang berdiskusi, dan punya ketertarikan besar pada presentasi, visual, serta koordinasi tim.
                    </p>
                </div>
                <button class="member-detail__close" type="button" aria-label="Tutup detail anggota"
                    data-close-member-detail>&times;</button>
            </header>
            <div class="member-detail__body" data-member-detail-body>
                <section class="member-detail__panel">
                    <h3>Tentang William</h3>
                    <p>Tipe orang yang nyaman memimpin diskusi, membagi tugas, dan membuat suasana kerja tetap jelas.
                        Lebih suka komunikasi yang langsung, terstruktur, dan tetap santai.</p>
                </section>
                <section class="member-detail__panel">
                    <h3>Minat & Tools</h3>
                    <div class="member-detail__pills" aria-label="Minat dan tools favorit">
                        <span class="member-detail__pill">Public Speaking</span>
                        <span class="member-detail__pill">Visual Planning</span>
                        <span class="member-detail__pill">Team Management</span>
                        <span class="member-detail__pill">Canva</span>
                        <span class="member-detail__pill">Notion</span>
                        <span class="member-detail__pill">Figma</span>
                    </div>
                </section>
                <section class="member-detail__panel">
                    <h3>Kepribadian</h3>
                    <ul class="member-detail__list">
                        <li>Suka membuat checklist agar pekerjaan terasa lebih ringan dan terarah.</li>
                        <li>Cukup tenang saat deadline, terutama kalau alurnya sudah jelas.</li>
                        <li>Senang menjadi penghubung antar orang supaya diskusi tidak berhenti di ide saja.</li>
                    </ul>
                </section>
                <section class="member-detail__panel">
                    <h3>Quick Facts</h3>
                    <div class="member-detail__stats" aria-label="Quick facts William Fransisco Sihotang">
                        <div class="member-detail__stat">
                            <strong>Style</strong>
                            <span>Structured but friendly</span>
                        </div>
                        <div class="member-detail__stat">
                            <strong>Mode</strong>
                            <span>Discussion first</span>
                        </div>
                        <div class="member-detail__stat">
                            <strong>Habit</strong>
                            <span>Notes before action</span>
                        </div>
                        <div class="member-detail__stat">
                            <strong>Goal</strong>
                            <span>Clear and confident delivery</span>
                        </div>
                    </div>
                </section>
            </div>
        </article>
    </div>

    <footer class="footer">
        <div>
            <h2>CityZen</h2>
            <p>Building the infrastructure for civic participation and sustainable urban development, one neighborhood
                at a time.</p>
            <small>&copy; 2026 CityZen Civic Tech</small>
        </div>
        <nav>
            <a href="#mission">Sustainability Manifesto</a>
            <a href="#features">Urban Data Privacy</a>
            <a href="#team">Open Source</a>
            <a href="#spaces">Public Spaces</a>
        </nav>
    </footer>

    <script>
        document.body.classList.add('anim-ready');

        const pulseElement = (element, duration = 360) => {
            let timer = element.dataset.pressTimer;
            if (timer) {
                window.clearTimeout(Number(timer));
            }

            element.classList.add('is-pressed');
            timer = window.setTimeout(() => {
                element.classList.remove('is-pressed');
                delete element.dataset.pressTimer;
            }, duration);
            element.dataset.pressTimer = String(timer);
        };

        document.querySelectorAll('.button').forEach((button) => {
            button.addEventListener('click', () => pulseElement(button, 260));
        });

        document.querySelectorAll('.nav a[href^="#"], .footer a[href^="#"]').forEach((link) => {
            link.addEventListener('click', (event) => {
                const target = document.querySelector(link.getAttribute('href'));
                if (!target) {
                    return;
                }

                event.preventDefault();
                const topbarHeight = document.querySelector('.topbar')?.offsetHeight ?? 0;
                const targetTop = target.getBoundingClientRect().top + window.scrollY - topbarHeight;
                window.scrollTo({
                    top: Math.max(targetTop, 0),
                    behavior: 'smooth',
                });
                history.pushState(null, '', link.getAttribute('href'));
            });
        });

        const revealItems = [...document.querySelectorAll('[data-reveal]')];

        if ('IntersectionObserver' in window) {
            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    entry.target.classList.add('is-visible');
                    revealObserver.unobserve(entry.target);
                });
            }, {
                rootMargin: '0px 0px -12% 0px',
                threshold: 0.16,
            });

            revealItems.forEach((item) => revealObserver.observe(item));
        } else {
            revealItems.forEach((item) => item.classList.add('is-visible'));
        }

        const memberDetail = document.querySelector('[data-member-detail]');
        const memberDetailClose = memberDetail?.querySelector('[data-close-member-detail]');
        const memberDetailCard = memberDetail?.querySelector('.member-detail__card');
        const memberDetailAvatar = memberDetail?.querySelector('[data-member-detail-avatar]');
        const memberDetailRole = memberDetail?.querySelector('[data-member-detail-role]');
        const memberDetailName = memberDetail?.querySelector('[data-member-detail-name]');
        const memberDetailSummary = memberDetail?.querySelector('[data-member-detail-summary]');
        const memberDetailBody = memberDetail?.querySelector('[data-member-detail-body]');
        const projectManagerDetailBody = memberDetailBody?.innerHTML ?? '';
        const escapeDetailText = (value = '') => String(value).replace(/[&<>"']/g, (char) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
        } [char]));
        const splitDetailItems = (value = '', limit = 0) => String(value)
            .split('|')
            .map((item) => item.trim())
            .filter(Boolean)
            .slice(0, limit);
        const padDetailItems = (items, length) => [...items, ...Array(Math.max(length - items.length, 0)).fill('')].slice(0,
            length);
        const buildEmptyMemberDetailBody = (card) => {
            const about = card.dataset.memberAbout || '';
            const interests = padDetailItems(splitDetailItems(card.dataset.memberInterests, 6), 6);
            const personality = padDetailItems(splitDetailItems(card.dataset.memberPersonality, 3), 3);
            const facts = padDetailItems(splitDetailItems(card.dataset.memberFacts, 4), 4);

            return `
                <section class="member-detail__panel member-detail__panel--blank">
                    <h3>Tentang</h3>
                    <p class="${about ? '' : 'member-detail__field--empty'}">${escapeDetailText(about)}</p>
                </section>
                <section class="member-detail__panel member-detail__panel--blank">
                    <h3>Minat & Tools</h3>
                    <div class="member-detail__pills" aria-label="Minat dan tools favorit">
                        ${interests.map((item) => `<span class="member-detail__pill${item ? '' : ' member-detail__pill--empty'}">${escapeDetailText(item)}</span>`).join('')}
                    </div>
                </section>
                <section class="member-detail__panel member-detail__panel--blank">
                    <h3>Kepribadian</h3>
                    <ul class="member-detail__list">
                        ${personality.map((item) => `<li>${escapeDetailText(item)}</li>`).join('')}
                    </ul>
                </section>
                <section class="member-detail__panel member-detail__panel--blank">
                    <h3>Quick Facts</h3>
                    <div class="member-detail__stats" aria-label="Quick facts anggota">
                        ${facts.map((item) => {
                            const [label = '', ...valueParts] = item.split(':');
                            return `<div class="member-detail__stat">
                                        <strong>${escapeDetailText(label.trim())}</strong>
                                        <span>${escapeDetailText(valueParts.join(':').trim())}</span>
                                    </div>`;
                        }).join('')}
                    </div>
                </section>`;
        };
        let activeMemberCard = null;

        const openMemberDetail = (card) => {
            if (!memberDetail || !card.dataset.memberCard) {
                return;
            }

            const hasFullDetail = card.dataset.memberCard === 'project-manager';

            activeMemberCard = card;
            if (memberDetailAvatar) {
                if (card.dataset.memberPhoto) {
                    memberDetailAvatar.style.overflow = 'hidden';
                    memberDetailAvatar.innerHTML =
                        `<img src="${card.dataset.memberPhoto}" style="width: 100%; height: 100%; object-fit: cover; border-radius: inherit;">`;
                } else {
                    memberDetailAvatar.style.overflow = 'visible';
                    memberDetailAvatar.innerHTML = '';
                    memberDetailAvatar.textContent = card.dataset.memberAvatar || '';
                }
            }
            if (memberDetailRole) memberDetailRole.textContent = card.dataset.memberRole || 'Personal Profile';
            if (memberDetailName) memberDetailName.textContent = card.dataset.memberName || '';
            if (memberDetailSummary) memberDetailSummary.textContent = card.dataset.memberSummary || '';
            if (memberDetailBody) {
                memberDetailBody.innerHTML = hasFullDetail ? projectManagerDetailBody : buildEmptyMemberDetailBody(
                    card);
                memberDetailBody.classList.remove('is-empty');
            }
            memberDetailCard?.classList.remove('is-head-only');
            card.setAttribute('aria-expanded', 'true');
            memberDetail.removeAttribute('inert');
            memberDetail.classList.add('is-open');
            memberDetail.setAttribute('aria-hidden', 'false');
            document.body.classList.add('detail-open');
            window.setTimeout(() => memberDetailClose?.focus(), 120);
        };

        const closeMemberDetail = () => {
            if (!memberDetail?.classList.contains('is-open')) {
                return;
            }

            memberDetail.classList.remove('is-open');
            memberDetail.setAttribute('aria-hidden', 'true');
            memberDetail.setAttribute('inert', '');
            document.body.classList.remove('detail-open');

            if (activeMemberCard) {
                activeMemberCard.setAttribute('aria-expanded', 'false');
                activeMemberCard.focus();
            }

            activeMemberCard = null;
        };

        memberDetail?.addEventListener('click', (event) => {
            if (event.target === memberDetail || event.target.closest('[data-close-member-detail]')) {
                closeMemberDetail();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeMemberDetail();
            }
        });

        document.querySelectorAll('[data-team-card]').forEach((card) => {
            const pressCard = () => pulseElement(card, 430);

            card.addEventListener('click', () => {
                pressCard();
                openMemberDetail(card);
            });
            card.addEventListener('keydown', (event) => {
                if (event.key !== 'Enter' && event.key !== ' ') {
                    return;
                }

                event.preventDefault();
                pressCard();
                openMemberDetail(card);
            });
        });
    </script>
</body>

</html>
