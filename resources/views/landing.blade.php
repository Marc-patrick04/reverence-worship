<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Reverence Worship — worship, music, evangelism, events, and community.">
    <title>{{ config('app.name', 'Reverence Worship') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root { --ink:#12231d; --green:#1d4d3b; --gold:#d8a84e; --cream:#f7f3e9; --muted:#64716b; }
        * { box-sizing:border-box; }
        html { scroll-behavior:smooth; }
        body { margin:0; color:var(--ink); font-family:"DM Sans",sans-serif; background:#fff; }
        img { display:block; max-width:100%; }
        a { color:inherit; text-decoration:none; }
        .wrap { width:min(1160px, calc(100% - 40px)); margin:auto; }
        .nav { position:fixed; inset:0 0 auto; z-index:50; background:rgba(13,34,27,.9); backdrop-filter:blur(14px); color:#fff; }
        .nav-inner { min-height:76px; display:flex; align-items:center; justify-content:space-between; gap:24px; }
        .brand { display:inline-flex; align-items:center; gap:11px; font-family:"Playfair Display",serif; font-size:1.35rem; font-weight:700; }
        .brand span { color:var(--gold); }
        .brand-logo { width:44px; height:44px; object-fit:contain; border-radius:12px; background:rgba(255,255,255,.1); padding:3px; }
        .links { display:flex; align-items:center; gap:25px; font-size:.9rem; }
        .links a:hover { color:#f2c873; }
        .login { border:1px solid rgba(255,255,255,.45); border-radius:999px; padding:9px 18px; }
        .menu { display:none; color:white; border:0; background:none; font-size:1.5rem; }
        .hero { min-height:760px; display:grid; place-items:center; position:relative; color:#fff; background:#173d30; overflow:hidden; }
        .hero-bg { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; opacity:0; transition:opacity 1.2s ease; }
        .hero-bg.active { opacity:.48; }
        .hero::after { content:""; position:absolute; inset:0; background:linear-gradient(90deg,rgba(8,30,23,.9),rgba(8,30,23,.25)); }
        .hero-content { position:relative; z-index:1; padding-top:70px; max-width:730px; margin-right:auto; }
        .eyebrow { color:#f4ca77; letter-spacing:.18em; text-transform:uppercase; font-size:.78rem; font-weight:700; }
        h1,h2 { font-family:"Playfair Display",serif; margin:0; }
        h1 { font-size:clamp(3rem,7vw,6rem); line-height:.98; margin:20px 0 25px; }
        .hero p { max-width:610px; font-size:1.13rem; line-height:1.75; color:#e2e9e5; }
        .actions { display:flex; gap:14px; margin-top:34px; flex-wrap:wrap; }
        .btn { display:inline-flex; align-items:center; justify-content:center; padding:13px 23px; border-radius:999px; font-weight:700; }
        .btn-gold { background:var(--gold); color:#17251f; }
        .btn-light { border:1px solid rgba(255,255,255,.55); color:#fff; }
        section { padding:100px 0; scroll-margin-top:70px; }
        .section-head { max-width:680px; margin-bottom:45px; }
        .section-head.center { text-align:center; margin-inline:auto; }
        h2 { font-size:clamp(2.2rem,4vw,3.5rem); margin:10px 0 15px; }
        .lead { color:var(--muted); line-height:1.75; }
        .about-grid { display:grid; grid-template-columns:1fr 1fr; gap:70px; align-items:center; }
        .about-art { min-height:430px; border-radius:28px; background:var(--green); padding:45px; display:flex; align-items:end; color:#fff; position:relative; overflow:hidden; }
        .about-art::before { content:"♪"; position:absolute; right:25px; top:-45px; font:260px "Playfair Display"; color:rgba(255,255,255,.07); }
        .about-art blockquote { margin:0; font:600 2rem/1.35 "Playfair Display"; position:relative; }
        .music { background:var(--cream); }
        .video-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:25px; }
        .video { background:#fff; border-radius:18px; overflow:hidden; box-shadow:0 12px 35px rgba(18,35,29,.08); }
        .ratio { aspect-ratio:16/9; }
        iframe { width:100%; height:100%; border:0; }
        .card-body { padding:18px 20px; font-weight:700; }
        .picture-grid { display:grid; grid-template-columns:repeat(12,1fr); gap:18px; }
        .picture { grid-column:span 4; border-radius:18px; overflow:hidden; position:relative; aspect-ratio:4/3; background:#e6e9e7; }
        .picture:first-child { grid-column:span 8; grid-row:span 2; aspect-ratio:auto; }
        .picture img { width:100%; height:100%; object-fit:cover; transition:.4s; }
        .picture:hover img { transform:scale(1.04); }
        .caption { position:absolute; inset:auto 0 0; padding:35px 18px 16px; color:#fff; background:linear-gradient(transparent,rgba(0,0,0,.75)); }
        .events { background:#102d24; color:#fff; }
        .events .lead { color:#b7c7c0; }
        .event-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; }
        .event { padding:28px; border:1px solid rgba(255,255,255,.13); border-radius:18px; background:rgba(255,255,255,.05); }
        .event time { color:#e9bd69; font-size:.82rem; font-weight:700; }
        .event h3 { font:600 1.4rem "Playfair Display"; margin:13px 0 10px; }
        .event p { color:#c2cec9; line-height:1.65; }
        .join { text-align:center; background:linear-gradient(135deg,#efe4ca,#fbf8f0); }
        .join .section-head { margin-bottom:25px; }
        .join .btn { background:var(--green); color:#fff; }
        .empty { grid-column:1/-1; padding:40px; border:1px dashed #aeb8b3; border-radius:18px; text-align:center; color:var(--muted); }
        footer { padding:35px 0; background:#091e17; color:#aebcb6; }
        .footer-inner { display:flex; align-items:center; justify-content:space-between; gap:20px; }
        .footer-brand { display:flex; align-items:center; gap:10px; color:#fff; }
        .footer-logo { width:38px; height:38px; object-fit:contain; }
        @media(max-width:800px) {
            .menu { display:block; }
            .links { display:none; position:absolute; top:76px; left:0; right:0; padding:25px; background:#0d221b; flex-direction:column; align-items:flex-start; }
            .links.open { display:flex; }
            .hero { min-height:680px; }
            .about-grid,.video-grid { grid-template-columns:1fr; }
            .picture,.picture:first-child { grid-column:span 12; aspect-ratio:4/3; }
            .event-grid { grid-template-columns:1fr; }
            section { padding:75px 0; }
            .footer-inner { flex-direction:column; }
        }
    </style>
</head>
<body>
    <header class="nav">
        <div class="wrap nav-inner">
            <a href="#home" class="brand">
                <img src="{{ asset('images/logo.png') }}" alt="Reverence Worship logo" class="brand-logo">
                <span style="color:white">Reverence <span>Worship</span></span>
            </a>
            <button class="menu" id="menuButton" aria-label="Open navigation" aria-expanded="false">☰</button>
            <nav class="links" id="navLinks" aria-label="Primary navigation">
                <a href="#home">Home</a><a href="#about">About us</a><a href="#music">Music</a>
                <a href="#pictures">Pictures</a><a href="#events">Events</a><a href="#join">Join us</a>
                @auth
                    <a class="login" href="{{ auth()->user()->isSuperAdmin() ? route('super-admin.dashboard') : route('user.dashboard') }}">Dashboard</a>
                @else
                    <a class="login" href="{{ route('login') }}">Login</a>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        <section class="hero" id="home">
            @foreach($heroPictures as $heroPicture)
                <img class="hero-bg {{ $loop->first ? 'active' : '' }}" src="{{ asset($heroPicture->image_path) }}" alt="{{ $heroPicture->title }}">
            @endforeach
            <div class="wrap">
                <div class="hero-content">
                    <div class="eyebrow">Music • Worship • Evangelism</div>
                    <h1>A sound of faith. A life of worship.</h1>
                    <p>{{ $heroPictures->first()->description ?? 'We are a community devoted to honoring God through music, authentic worship, fellowship, and the message of hope.' }}</p>
                    <div class="actions">
                        <a class="btn btn-gold" href="#music">Explore our music</a>
                        <a class="btn btn-light" href="#join">Join the community</a>
                    </div>
                </div>
            </div>
        </section>

        <section id="about">
            <div class="wrap about-grid">
                <div class="about-art"><blockquote>“Let everything that has breath praise the Lord.”</blockquote></div>
                <div>
                    <div class="eyebrow">About us</div>
                    <h2>More than music, it is our ministry.</h2>
                    <p class="lead">Reverence Worship brings singers, musicians, worshippers, and evangelists together to serve with excellence and humility. Our public board shares the latest sound, stories, and moments from our ministry.</p>
                    <a class="btn btn-gold" href="#events">See what is happening</a>
                </div>
            </div>
        </section>

        <section class="music" id="music">
            <div class="wrap">
                <div class="section-head center"><div class="eyebrow">Listen & worship</div><h2>Our music</h2><p class="lead">Published from the Music & Evangelism Public Board.</p></div>
                <div class="video-grid">
                    @forelse($videos as $video)
                        <article class="video"><div class="ratio"><iframe src="https://www.youtube-nocookie.com/embed/{{ urlencode($video->youtube_id) }}" title="{{ $video->title }}" loading="lazy" allowfullscreen></iframe></div><div class="card-body">{{ $video->title }}</div></article>
                    @empty
                        <div class="empty">New worship music will appear here when it is published on the Public Board.</div>
                    @endforelse
                </div>
            </div>
        </section>

        <section id="pictures">
            <div class="wrap">
                <div class="section-head"><div class="eyebrow">Our story in frames</div><h2>Pictures</h2><p class="lead">Moments selected and published by the Music & Evangelism team.</p></div>
                <div class="picture-grid">
                    @forelse($pictures as $picture)
                        <figure class="picture"><img src="{{ asset($picture->image_path) }}" alt="{{ $picture->title }}" loading="lazy"><figcaption class="caption"><strong>{{ $picture->title }}</strong>@if($picture->description)<br><small>{{ $picture->description }}</small>@endif</figcaption></figure>
                    @empty
                        <div class="empty">Published pictures from the Public Board will appear here.</div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="events" id="events">
            <div class="wrap">
                <div class="section-head"><div class="eyebrow">Stay connected</div><h2>Events & updates</h2><p class="lead">The latest notices published by Music & Evangelism.</p></div>
                <div class="event-grid">
                    @forelse($events as $event)
                        <article class="event">
                            <time datetime="{{ ($event->event_date ?? $event->created_at)->toDateString() }}">
                                {{ ($event->type ?? 'update') === 'event' ? 'Event' : 'Update' }} · {{ ($event->event_date ?? $event->created_at)->format('d M Y') }}
                                @if($event->event_date) · {{ $event->event_date->format('H:i') }}@endif
                            </time>
                            <h3>{{ $event->title }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit($event->content, 180) }}</p>
                        </article>
                    @empty
                        <div class="empty">Upcoming events and ministry updates will appear here.</div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="join" id="join">
            <div class="wrap">
                <div class="section-head center"><div class="eyebrow">You belong here</div><h2>Join us in worship</h2><p class="lead">Create your account to become part of the Reverence Worship community and stay connected with the ministry.</p></div>
                <a class="btn" href="{{ route('register') }}">Join Reverence Worship</a>
            </div>
        </section>
    </main>

    <footer><div class="wrap footer-inner"><div class="footer-brand"><img src="{{ asset('images/logo.png') }}" alt="" class="footer-logo"><strong>{{ config('app.name', 'Reverence Worship') }}</strong></div><span>© {{ date('Y') }} Reverence Worship. All rights reserved.</span></div></footer>
    <script>
        const menuButton = document.getElementById('menuButton');
        const navLinks = document.getElementById('navLinks');
        menuButton.addEventListener('click', () => {
            const open = navLinks.classList.toggle('open');
            menuButton.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        navLinks.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
            navLinks.classList.remove('open');
            menuButton.setAttribute('aria-expanded', 'false');
        }));
        const heroImages = Array.from(document.querySelectorAll('.hero-bg'));
        if (heroImages.length > 1) {
            let heroIndex = 0;
            window.setInterval(() => {
                heroImages[heroIndex].classList.remove('active');
                heroIndex = (heroIndex + 1) % heroImages.length;
                heroImages[heroIndex].classList.add('active');
            }, 10000);
        }
    </script>
</body>
</html>
