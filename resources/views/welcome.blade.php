<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sahadat-Qu</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito-sans:400,500,600,700,800" rel="stylesheet" />
    <style>
        :root {
            --blue: #0f65ff;
            --blue-dark: #0346c8;
            --ink: #202832;
            --muted: #68717d;
            --line: #dbe1e8;
            --soft: #f2f5f9;
            --panel: #eef2f7;
            --footer: #68717a;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            color: var(--ink);
            background: #ffffff;
            font-family: "Nunito Sans", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        img {
            display: block;
            max-width: 100%;
        }

        .container {
            width: min(100% - 40px, 1180px);
            margin-inline: auto;
        }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 20;
            border-bottom: 1px solid rgba(219, 225, 232, .65);
            background: rgba(255, 255, 255, .92);
            backdrop-filter: blur(14px);
        }

        .nav {
            display: flex;
            min-height: 66px;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
            font-weight: 700;
        }

        .brand img {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            object-fit: cover;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 30px;
            font-size: 13px;
            font-weight: 600;
        }

        .nav-links a {
            transition: color .2s ease;
        }

        .nav-links a:hover {
            color: var(--blue);
        }

        .btn {
            display: inline-flex;
            min-height: 42px;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: 1px solid var(--blue);
            border-radius: 999px;
            padding: 0 24px;
            color: var(--blue);
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0;
            transition: background .2s ease, color .2s ease, border-color .2s ease, transform .2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: var(--blue);
            color: #ffffff;
        }

        .btn-primary:hover {
            background: var(--blue-dark);
            border-color: var(--blue-dark);
        }

        .hero .btn {
            border-color: rgba(255, 255, 255, .82);
            color: #ffffff;
            background: rgba(255, 255, 255, .08);
        }

        .hero .btn-primary {
            border-color: var(--blue);
            background: var(--blue);
        }

        .hero .btn:hover {
            background: rgba(255, 255, 255, .18);
        }

        .hero .btn-primary:hover {
            border-color: var(--blue-dark);
            background: var(--blue-dark);
        }

        .btn-small {
            min-height: 36px;
            border-radius: 6px;
            padding-inline: 22px;
            font-size: 12px;
        }

        .hero {
            position: relative;
            overflow: hidden;
            min-height: min(680px, calc(100vh - 86px));
            background: #07111f;
            color: #ffffff;
        }

        .hero::after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 1;
            background:
                linear-gradient(90deg, rgba(3, 10, 25, .84) 0%, rgba(3, 10, 25, .64) 43%, rgba(3, 10, 25, .22) 100%),
                linear-gradient(0deg, rgba(3, 10, 25, .38), rgba(3, 10, 25, .08));
        }

        .hero-slides {
            position: absolute;
            inset: 0;
        }

        .hero-slide {
            position: absolute;
            inset: 0;
            background-position: center;
            background-size: cover;
            opacity: 0;
            transform: scale(1.04);
            animation: heroSlide 18s infinite;
        }

        .hero-slide:nth-child(2) {
            animation-delay: 6s;
        }

        .hero-slide:nth-child(3) {
            animation-delay: 12s;
        }

        @keyframes heroSlide {
            0% {
                opacity: 0;
                transform: scale(1.04);
            }
            8%,
            33% {
                opacity: 1;
                transform: scale(1);
            }
            41%,
            100% {
                opacity: 0;
                transform: scale(1.02);
            }
        }

        .hero-inner {
            position: relative;
            z-index: 2;
            display: flex;
            min-height: min(680px, calc(100vh - 86px));
            align-items: center;
            padding: 76px 0 68px;
        }

        .hero h1 {
            max-width: 720px;
            margin: 0;
            font-size: clamp(42px, 6vw, 66px);
            line-height: .98;
            letter-spacing: 0;
        }

        .hero p {
            max-width: 610px;
            margin: 34px 0 32px;
            color: rgba(255, 255, 255, .88);
            font-size: 15px;
            line-height: 1.7;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
        }

        .image-placeholder {
            position: relative;
            overflow: hidden;
            border: 1px solid var(--line);
            background: #f7f9fc;
        }

        .image-placeholder::before,
        .image-placeholder::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            background: #d7dde5;
        }

        .image-placeholder::before {
            width: 120px;
            height: 120px;
            left: 22%;
            top: 18%;
        }

        .image-placeholder::after {
            width: 56px;
            height: 56px;
            right: 24%;
            bottom: 17%;
        }

        .hero-card {
            width: min(280px, 80%);
            aspect-ratio: 1.2;
            border: 22px solid #d7dde5;
            border-radius: 28px;
            background:
                linear-gradient(135deg, transparent 48%, #d7dde5 49% 64%, transparent 65%),
                linear-gradient(45deg, transparent 55%, #c9d0d9 56% 72%, transparent 73%),
                #f8fafc;
        }

        .hero-card::before {
            width: 38px;
            height: 38px;
            left: 28px;
            top: 28px;
        }

        .slider-dots {
            position: absolute;
            left: 50%;
            bottom: 26px;
            z-index: 3;
            transform: translateX(-50%);
            display: flex;
            justify-content: center;
            gap: 7px;
        }

        .slider-dots span {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .55);
        }

        .slider-dots span:first-child {
            width: 42px;
            background: #ffffff;
            animation: dotOne 18s infinite;
        }

        .slider-dots span:nth-child(2) {
            animation: dotTwo 18s infinite;
        }

        .slider-dots span:nth-child(3) {
            animation: dotThree 18s infinite;
        }

        @keyframes dotOne {
            0%, 33% { width: 42px; background: #ffffff; }
            41%, 100% { width: 8px; background: rgba(255, 255, 255, .55); }
        }

        @keyframes dotTwo {
            0%, 33%, 74%, 100% { width: 8px; background: rgba(255, 255, 255, .55); }
            41%, 66% { width: 42px; background: #ffffff; }
        }

        @keyframes dotThree {
            0%, 66% { width: 8px; background: rgba(255, 255, 255, .55); }
            74%, 100% { width: 42px; background: #ffffff; }
        }

        .section {
            padding: 82px 0;
        }

        .about-section {
            padding: 96px 0 84px;
        }

        .section-soft {
            background: var(--panel);
        }

        .section-title {
            max-width: 950px;
            margin: 0 auto 58px;
            text-align: center;
        }

        .eyebrow {
            margin: 0 0 8px;
            color: #003c9e;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: .02em;
            text-transform: uppercase;
        }

        .section-title h2,
        .split-copy h2 {
            margin: 0;
            font-size: clamp(30px, 4vw, 42px);
            line-height: 1.08;
            letter-spacing: 0;
        }

        .section-title p {
            max-width: 1040px;
            margin: 34px auto 0;
            color: #384655;
            font-size: 14px;
            line-height: 1.65;
        }

        .about-grid,
        .donation-grid {
            display: grid;
            align-items: center;
            grid-template-columns: .9fr 1fr;
            gap: 72px;
        }

        .about-section .section-title {
            margin-bottom: 58px;
        }

        .about-section .eyebrow {
            color: var(--blue);
            font-size: 13px;
            letter-spacing: 0;
        }

        .about-section .section-title h2 {
            color: #0f2d5c;
            font-size: clamp(38px, 4.8vw, 48px);
            line-height: 1.05;
        }

        .about-section .about-grid {
            grid-template-columns: minmax(360px, 525px) 1fr;
            gap: 82px;
        }

        .window-art {
            aspect-ratio: 1.62;
            border-radius: 8px;
            border: 1px solid #d7dee8;
            background:
                radial-gradient(circle at 78% 36%, #d7dde5 0 35px, transparent 36px),
                radial-gradient(circle at 70% 42%, #e8edf3 0 54px, transparent 55px),
                linear-gradient(160deg, transparent 55%, #d8dee6 56% 73%, transparent 74%),
                linear-gradient(170deg, transparent 48%, #e8edf3 49% 63%, transparent 64%),
                #f7f9fc;
        }

        .about-section .window-art {
            align-self: start;
            min-height: 324px;
            box-shadow: none;
        }

        .about-photo-card {
            overflow: hidden;
            align-self: start;
            min-height: 324px;
            border: 1px solid #d7dee8;
            border-radius: 8px;
            background: #eef2f7;
        }

        .about-photo-bar {
            display: flex;
            gap: 8px;
            align-items: center;
            height: 42px;
            border-bottom: 1px solid #d7dee8;
            background: #ffffff;
            padding: 0 16px;
        }

        .about-photo-bar span {
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: #cfd8e3;
        }

        .about-photo-card img {
            width: 100%;
            height: 282px;
            object-fit: cover;
        }

        .window-art::before {
            width: 120px;
            height: 120px;
            left: 28%;
            top: 30%;
        }

        .window-bar {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 42px;
            border-bottom: 1px solid #e5eaf0;
            background: #ffffff;
        }

        .window-bar span {
            position: absolute;
            top: 16px;
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #d8dee6;
        }

        .window-bar span:nth-child(1) {
            left: 16px;
        }

        .window-bar span:nth-child(2) {
            left: 32px;
        }

        .window-bar span:nth-child(3) {
            left: 48px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 48px 54px;
        }

        .about-section .feature-grid {
            align-self: start;
            grid-template-columns: repeat(2, minmax(190px, 1fr));
            gap: 48px 66px;
            padding-top: 22px;
        }

        .feature {
            display: grid;
            grid-template-columns: 34px 1fr;
            gap: 14px;
        }

        .about-section .feature {
            grid-template-columns: 34px 1fr;
            gap: 16px;
        }

        .feature-icon,
        .program-icon,
        .author-icon,
        .social-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .feature-icon {
            width: 34px;
            height: 34px;
            color: #aeb8c4;
        }

        .about-section .feature-icon {
            display: block;
            width: 40px;
            height: 40px;
            margin-top: 0;
        }

        .about-section .feature-icon img {
            width: 40px;
            height: 40px;
        }

        .feature h3 {
            margin: 0 0 12px;
            font-size: 15px;
            line-height: 1.25;
        }

        .about-section .feature h3 {
            margin-bottom: 13px;
            color: #111827;
            font-size: 15px;
            font-weight: 800;
        }

        .feature p,
        .split-copy p {
            margin: 0;
            color: #344150;
            font-size: 13px;
            line-height: 1.65;
        }

        .about-section .feature p {
            max-width: 250px;
            color: #1f3554;
            font-size: 13.5px;
            line-height: 1.62;
        }

        .programs {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 24px 36px;
            margin-top: 42px;
            color: #6a747f;
            font-size: 18px;
            font-weight: 800;
        }

        .program-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .program-icon {
            color: #69737d;
        }

        .news-grid,
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .article-card {
            border: 1px solid var(--line);
            background: #ffffff;
        }

        .article-thumb,
        .contact-thumb {
            display: flex;
            min-height: 190px;
            align-items: center;
            justify-content: center;
            background: #d9dee5;
            color: #ffffff;
        }

        .article-body {
            padding: 22px 18px 28px;
        }

        .article-meta {
            margin: 0 0 4px;
            font-size: 11px;
            font-weight: 700;
        }

        .article-card h3 {
            margin: 0 0 12px;
            font-size: 15px;
            line-height: 1.2;
        }

        .article-card p {
            margin: 0;
            color: #33404d;
            font-size: 12px;
            line-height: 1.6;
        }

        .author {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 24px;
            font-size: 11px;
        }

        .author strong {
            display: block;
            font-size: 12px;
        }

        .author-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #f2f5f9;
            color: #b8c1cc;
        }

        .center-action {
            display: flex;
            justify-content: center;
            margin-top: 58px;
        }

        .donation-grid {
            grid-template-columns: 1fr .95fr;
        }

        .split-copy .eyebrow {
            margin-bottom: 14px;
        }

        .split-copy p {
            margin-top: 30px;
        }

        .split-copy .actions {
            margin-top: 34px;
        }

        .contact-card {
            text-align: center;
        }

        .contact-thumb {
            min-height: 262px;
            margin-bottom: 18px;
            background: #b7bec6;
        }

        .contact-card h3 {
            margin: 0 0 6px;
            font-size: 14px;
        }

        .contact-card p {
            margin: 0 0 14px;
            color: #46515e;
            font-size: 12px;
            line-height: 1.4;
        }

        .contact-card .btn {
            min-height: 38px;
            min-width: 160px;
            border-radius: 0;
            padding-inline: 16px;
            font-size: 12px;
        }

        .social-title {
            margin: 66px 0 24px;
            text-align: center;
            font-size: 19px;
            font-weight: 800;
        }

        .socials {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .social-icon {
            width: 42px;
            height: 42px;
            color: #101820;
        }

        .site-footer {
            background: var(--footer);
            color: #ffffff;
        }

        .footer-inner {
            display: flex;
            min-height: 122px;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            font-size: 12px;
        }

        .footer-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 28px;
        }

        .footer-socials {
            display: flex;
            gap: 14px;
        }

        .copyright {
            padding: 0 0 30px;
            text-align: center;
            color: rgba(255, 255, 255, .85);
            font-size: 11px;
        }

        .icon {
            width: 1em;
            height: 1em;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
        }

        .fill-icon {
            fill: currentColor;
            stroke: none;
        }

        @media (max-width: 980px) {
            .nav {
                align-items: flex-start;
                flex-direction: column;
                padding: 16px 0;
            }

            .nav-links {
                width: 100%;
                flex-wrap: wrap;
                gap: 16px 22px;
            }

            .about-grid,
            .donation-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .about-section .about-grid {
                grid-template-columns: 1fr;
                gap: 42px;
            }

            .hero-inner {
                padding-top: 56px;
            }

            .news-grid,
            .contact-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 22px;
            }

            .footer-inner {
                flex-direction: column;
                padding: 28px 0;
            }
        }

        @media (max-width: 640px) {
            .container {
                width: min(100% - 28px, 1180px);
            }

            .nav-links {
                font-size: 12px;
            }

            .hero h1 {
                font-size: 38px;
            }

            .hero,
            .hero-inner {
                min-height: 600px;
            }

            .section {
                padding: 64px 0;
            }

            .section-title {
                margin-bottom: 38px;
            }

            .feature-grid,
            .news-grid,
            .contact-grid {
                grid-template-columns: 1fr;
            }

            .about-section {
                padding: 72px 0 64px;
            }

            .about-section .feature-grid {
                grid-template-columns: 1fr;
                gap: 30px;
                padding-top: 0;
            }

            .about-section .window-art {
                min-height: 220px;
            }

            .about-photo-card,
            .about-photo-card img {
                min-height: 240px;
            }

            .programs {
                align-items: flex-start;
                flex-direction: column;
                font-size: 16px;
            }

            .article-thumb,
            .contact-thumb {
                min-height: 210px;
            }

            .actions {
                align-items: stretch;
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="container nav">
            <a class="brand" href="#beranda" aria-label="Sahadat-Qu">
                <img src="{{ asset('logo-sahadat-qu.jpeg') }}" alt="Logo Sahadat-Qu">
                <span>Sahadat-Qu</span>
            </a>
            <nav class="nav-links" aria-label="Navigasi utama">
                <a href="#beranda">Beranda</a>
                <a href="#tentang">Tentang Kami</a>
                <a href="#program">Program Pendidikan</a>
                <a href="#berita">Berita</a>
                <a href="#donasi">Donasi</a>
                <a href="#kontak">Kontak</a>
                <a href="#ecommerce">E-Commerce</a>
                <a class="btn btn-primary btn-small" href="{{ route('login') }}">Masuk</a>
            </nav>
        </div>
    </header>

    <main>
        <section id="beranda" class="hero">
            <div class="hero-slides" aria-hidden="true">
                <div class="hero-slide" style="background-image: url('https://images.pexels.com/photos/20627702/pexels-photo-20627702.jpeg?auto=compress&cs=tinysrgb&w=1800');"></div>
                <div class="hero-slide" style="background-image: url('https://images.pexels.com/photos/27976845/pexels-photo-27976845.jpeg?auto=compress&cs=tinysrgb&w=1800');"></div>
                <div class="hero-slide" style="background-image: url('https://images.pexels.com/photos/30890552/pexels-photo-30890552.jpeg?auto=compress&cs=tinysrgb&w=1800');"></div>
            </div>
            <div class="container hero-inner">
                <div>
                    <h1>Mewujudkan Muslim Cinta Al-Qur'an</h1>
                    <p>Rumah Tahfidz Sahadat-Qu hadir sebagai ruang belajar Al-Qur'an yang hangat, disiplin, dan membangun karakter santri agar tumbuh berakhlak serta mandiri.</p>
                    <div class="actions">
                        <a class="btn btn-primary" href="{{ route('login') }}">MASUK
                            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </a>
                        <a class="btn" href="#pendaftaran">PENDAFTARAN</a>
                    </div>
                </div>
            </div>
            <div class="slider-dots" aria-hidden="true">
                <span></span><span></span><span></span>
            </div>
        </section>

        <section id="tentang" class="section about-section">
            <div class="container">
                <div class="section-title">
                    <p class="eyebrow">Tentang Kami</p>
                    <h2>Rumah Tahfidz Sahadat-Qu</h2>
                </div>
                <div class="about-grid">
                    <div class="about-photo-card">
                        <div class="about-photo-bar" aria-hidden="true"><span></span><span></span><span></span></div>
                        <img src="https://images.pexels.com/photos/27976845/pexels-photo-27976845.jpeg?auto=compress&cs=tinysrgb&w=1200" alt="Suasana belajar Al-Qur'an">
                    </div>
                    <div class="feature-grid">
                        <article class="feature">
                            <div class="feature-icon">
                                <img src="{{ asset('icons/profile-lembaga.svg') }}" alt="" aria-hidden="true">
                            </div>
                            <div>
                                <h3>Profil Lembaga</h3>
                                <p>Wadah pembinaan tahfidz yang menanamkan cinta Al-Qur'an melalui pembelajaran yang terarah dan menyenangkan.</p>
                            </div>
                        </article>
                        <article class="feature">
                            <div class="feature-icon">
                                <img src="{{ asset('icons/visi-misi.svg') }}" alt="" aria-hidden="true">
                            </div>
                            <div>
                                <h3>Visi Misi</h3>
                                <p>Mencetak generasi Qur'ani yang berakhlak mulia, berilmu, mandiri, dan memberi manfaat untuk masyarakat.</p>
                            </div>
                        </article>
                        <article class="feature">
                            <div class="feature-icon">
                                <img src="{{ asset('icons/struktur-organisasi.svg') }}" alt="" aria-hidden="true">
                            </div>
                            <div>
                                <h3>Struktur Organisasi</h3>
                                <p>Didukung pengurus dan pembimbing yang bekerja bersama dalam pelayanan pendidikan serta pendampingan santri.</p>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <section id="program" class="section section-soft">
            <div class="container">
                <div class="section-title">
                    <p class="eyebrow">Program Pendidikan</p>
                    <h2>Membangun Generasi Qur'an yang Berakhlak dan Mandiri</h2>
                    <p>Program pendidikan di Rumah Tahfidz Sahadat-Qu dirancang untuk membentuk generasi Qur'an yang berakhlak mulia, mandiri, dan memiliki keterampilan hidup. Melalui pembinaan tahfidz Al-Qur'an serta pengembangan potensi santri dalam bidang sosial, kewirausahaan, dan keterampilan, lembaga berkomitmen mencetak generasi yang bermanfaat bagi umat dan masyarakat.</p>
                </div>
                <div class="programs">
                    <div class="program-item"><span class="program-icon"><svg class="icon" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5z"/></svg></span>Al-Qur'an</div>
                    <div class="program-item"><span class="program-icon"><svg class="icon" viewBox="0 0 24 24"><path d="M7 20h10"/><path d="M10 20c5-4 6-9 4-16"/><path d="M9 9c-2 0-4-2-4-5 4 0 7 2 7 5"/><path d="M15 13c2.5 0 4-2 4-5-4 0-7 2-7 5"/></svg></span>Pertanian Hidayah</div>
                    <div class="program-item"><span class="program-icon"><svg class="icon" viewBox="0 0 24 24"><path d="M14 9V5a3 3 0 0 0-6 0v4"/><path d="M5 11h14l-1.5 9h-11z"/><path d="M16 11a4 4 0 0 1-8 0"/></svg></span>Bakti Sosial</div>
                    <div class="program-item"><span class="program-icon"><svg class="icon" viewBox="0 0 24 24"><path d="M3 7h11l2 5h5l-2 7H8L6 4H3"/><path d="M10 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/><path d="M18 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/></svg></span>Thibbun Nabawi</div>
                    <div class="program-item"><span class="program-icon"><svg class="icon" viewBox="0 0 24 24"><path d="M9 18h6"/><path d="M10 22h4"/><path d="M12 2a7 7 0 0 0-4 12c.7.6 1 1.2 1 2h6c0-.8.3-1.4 1-2a7 7 0 0 0-4-12z"/></svg></span>Entrepreneur</div>
                    <div class="program-item"><span class="program-icon"><svg class="icon" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg></span>Karya Tulis</div>
                </div>
            </div>
        </section>

        <section id="berita" class="section">
            <div class="container">
                <div class="section-title">
                    <p class="eyebrow">Berita</p>
                    <h2>Informasi, Kegiatan, dan Perkembangan Terbaru Rumah Tahfidz SahadatQu</h2>
                </div>
                <div class="news-grid">
                    @for ($i = 0; $i < 4; $i++)
                        <article class="article-card">
                            <div class="article-thumb" aria-hidden="true">
                                <svg width="72" height="72" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v14H4z" fill="none" stroke="currentColor" stroke-width="1.6"/><circle cx="9" cy="10" r="1.5" fill="currentColor"/><path d="m7 16 3-3 2 2 3-4 3 5z" fill="currentColor"/></svg>
                            </div>
                            <div class="article-body">
                                <p class="article-meta">Kategori</p>
                                <h3>Judul Artikel</h3>
                                <p>Kegiatan santri dan perkembangan terbaru lembaga tersaji sebagai kabar baik untuk keluarga besar Sahadat-Qu.</p>
                                <div class="author">
                                    <span class="author-icon"><svg class="icon" viewBox="0 0 24 24"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg></span>
                                    <span><strong>Nama Penulis</strong>Jabatan</span>
                                </div>
                            </div>
                        </article>
                    @endfor
                </div>
                <div class="center-action">
                    <a class="btn btn-primary btn-small" href="#berita-lainnya">Lihat Selengkapnya</a>
                </div>
            </div>
        </section>

        <section id="donasi" class="section section-soft">
            <div class="container donation-grid">
                <div class="image-placeholder window-art" aria-hidden="true">
                    <div class="window-bar"><span></span><span></span><span></span></div>
                </div>
                <div class="split-copy">
                    <p class="eyebrow">Donasi</p>
                    <h2>Salurkan Donasi Terbaik untuk Mendukung Sesama</h2>
                    <p>Dukungan Anda membantu keberlangsungan pendidikan Al-Qur'an, fasilitas belajar, dan program sosial untuk santri serta masyarakat sekitar.</p>
                    <div class="actions">
                        <a class="btn btn-primary" href="#konfirmasi-donasi">Konfirmasi donasi</a>
                        <a class="btn" href="#pelajari-donasi">Pelajari Selengkapnya</a>
                    </div>
                </div>
            </div>
        </section>

        <section id="kontak" class="section">
            <div class="container">
                <div class="section-title">
                    <p class="eyebrow">Kontak</p>
                    <h2>Informasi Kontak dan Layanan Rumah Tahfidz SahadatQu</h2>
                </div>
                <div class="contact-grid">
                    @foreach (['Pusat', 'Cabang 1', 'Cabang 2', 'Cabang 3'] as $branch)
                        <article class="contact-card">
                            <div class="contact-thumb" aria-hidden="true">
                                <svg width="78" height="78" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v14H4z" fill="none" stroke="currentColor" stroke-width="1.5"/><circle cx="9" cy="10" r="1.5" fill="currentColor"/><path d="m7 16 3-3 2 2 3-4 3 5z" fill="currentColor"/></svg>
                            </div>
                            <h3>{{ $branch }}</h3>
                            <p>Nama Narahubung<br>Nomor narahubung</p>
                            <a class="btn" href="#hubungi-{{ Str::slug($branch) }}">Hubungi</a>
                        </article>
                    @endforeach
                </div>
                <h3 class="social-title">Sosial Media SahadatQu</h3>
                <div class="socials" aria-label="Sosial media">
                    <a class="social-icon" href="#" aria-label="YouTube"><svg class="icon fill-icon" viewBox="0 0 24 24"><path d="M21.6 7.2s-.2-1.6-.9-2.3c-.9-.9-1.9-.9-2.4-1C15 3.7 12 3.7 12 3.7s-3 0-6.3.2c-.5.1-1.5.1-2.4 1-.7.7-.9 2.3-.9 2.3S2.2 9 2.2 10.8v1.7c0 1.8.2 3.6.2 3.6s.2 1.6.9 2.3c.9.9 2.1.9 2.7 1 1.9.2 6 .2 6 .2s3 0 6.3-.2c.5-.1 1.5-.1 2.4-1 .7-.7.9-2.3.9-2.3s.2-1.8.2-3.6v-1.7c0-1.8-.2-3.6-.2-3.6ZM10 15.1V8.8l5.7 3.1z"/></svg></a>
                    <a class="social-icon" href="#" aria-label="Facebook"><svg class="icon fill-icon" viewBox="0 0 24 24"><path d="M14 8h3V4h-3c-3.1 0-5 1.9-5 5v3H6v4h3v7h4v-7h3.4l.6-4h-4V9c0-.7.3-1 1-1Z"/></svg></a>
                    <a class="social-icon" href="#" aria-label="TikTok"><svg class="icon fill-icon" viewBox="0 0 24 24"><path d="M16 3c.5 2.7 2.1 4.4 5 4.6v4.1c-1.7.1-3.3-.4-4.9-1.4v5.9c0 4.3-4.7 7.1-8.6 4.9-5-2.8-3.5-10.4 2.2-11.1.6-.1 1.2 0 1.8.1v4.3c-.4-.1-.8-.2-1.2-.1-2.1.3-2.8 3.2-1 4.2 1.6.9 3.5-.3 3.5-2.1V3Z"/></svg></a>
                    <a class="social-icon" href="#" aria-label="Instagram"><svg class="icon" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5h.01"/></svg></a>
                    <a class="social-icon" href="#" aria-label="Email"><svg class="icon fill-icon" viewBox="0 0 24 24"><path d="M3 5h18v14H3zm9 8 7.2-6H4.8zm-1.1 1.3L5 9.4V17h14V9.4l-5.9 4.9a1.7 1.7 0 0 1-2.2 0Z"/></svg></a>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container footer-inner">
            <a class="brand" href="#beranda">
                <img src="{{ asset('logo-sahadat-qu.jpeg') }}" alt="Logo Sahadat-Qu">
                <span>SahadatQu</span>
            </a>
            <nav class="footer-links" aria-label="Navigasi footer">
                <a href="#beranda">Beranda</a>
                <a href="#tentang">Tentang Kami</a>
                <a href="#program">Program Pendidikan</a>
                <a href="#berita">Berita</a>
                <a href="#donasi">Donasi</a>
                <a href="#kontak">Kontak</a>
            </nav>
            <div class="footer-socials" aria-label="Sosial media footer">
                <span>YT</span><span>f</span><span>Tk</span><span>IG</span><span>M</span>
            </div>
        </div>
        <div class="copyright">SahadatQu &copy; 2026. All rights reserved</div>
    </footer>
</body>
</html>
