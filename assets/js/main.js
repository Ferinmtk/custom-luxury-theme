/* Luxury Homes — front-end behaviour
   1. Descent hero (approved tester design): gallery-mat sizing,
      scroll-scrubbed video (lerp-smoothed currentTime), 3 phase
      copy blocks, altimeter, scroll cue, reduced-motion fallback
   2. Homes by style: pinned horizontal scrub gallery, progress bar,
      static swipe-rail fallback (touch / reduced motion), card image
      parallax entry
   3. Reveal-on-scroll
*/
(function () {
    'use strict';

    // Clear any stale scroll lock left from a previous state.
    document.body.style.overflow = '';
    document.body.classList.remove('is-locked');

    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var clamp = function (v, a, b) {
        return Math.max(a, Math.min(b, v));
    };
    var map = function (v, a, b) {
        return clamp((v - a) / (b - a), 0, 1);
    };

    /**
     * Seek a scroll-scrubbed video safely.
     *
     * Assigning currentTime every animation frame issues a new seek before the
     * previous one has resolved. On a warm cache that is harmless; on a cold one
     * each seek needs a byte-range request, so the requests cancel each other and
     * the video appears frozen. Only seek when the element is ready, no seek is
     * already in flight, and the target time is actually buffered.
     */
    var isBuffered = function (vid, t) {
        var b = vid.buffered;
        for (var i = 0; i < b.length; i++) {
            if (t >= b.start(i) && t <= b.end(i)) {
                return true;
            }
        }
        return false;
    };

    var seekVideo = function (vid, t) {
        if (vid.readyState < 1) {
            return;
        }           // no metadata, no duration
        if (vid.seeking) {
            return;
        }                  // a seek is already pending
        if (Math.abs(vid.currentTime - t) <= 0.01) {
            return;
        }

        if (!isBuffered(vid, t)) {
            // Seeking is also how we ask the browser to fetch that range, so we
            // can't simply refuse. Do it, but at most a few times a second, or
            // the requests cancel one another and nothing ever arrives.
            var now = Date.now();
            if (now - (vid._lhLastFetchSeek || 0) < 250) {
                return;
            }
            vid._lhLastFetchSeek = now;
        }

        try {
            vid.currentTime = t;
        } catch (e) { /* not seekable yet */
        }
    };

    /* ---------- 1. Descent hero ---------- */

    var seq = document.getElementById('descent');
    var vid = document.getElementById('vid');

    if (seq && vid) {
        // Lighter encode on small screens (set before metadata loads).
        var mobileSrc = vid.getAttribute('data-mobile-src');
        if (mobileSrc && window.matchMedia('(max-width: 720px)').matches) {
            vid.src = mobileSrc;
        }

        if (reducedMotion) {
            vid.loop = true;
            vid.autoplay = true;
            vid.play().catch(function () {
            });
        } else {
            var mat = document.getElementById('mat');
            var c1 = document.getElementById('c1');
            var c2 = document.getElementById('c2');
            var c3 = document.getElementById('c3');
            var altNum = document.getElementById('altNum');
            var altDot = document.getElementById('altDot');
            var alt = document.querySelector('.descent .alt');
            var cue = document.getElementById('dCue');
            var START_ALT = 400;
            var dur = 0;
            var target = 0;
            var current = 0;
            var ticking = false;

            vid.addEventListener('loadedmetadata', function () {
                dur = vid.duration;
                vid.pause();
            });

            var setCopy = function (el, vis) {
                el.style.opacity = vis;
                el.style.transform = 'translateY(' + ((1 - vis) * 24) + 'px)';
            };

            var onScroll = function () {
                var total = seq.offsetHeight - window.innerHeight;
                var p = map(-seq.getBoundingClientRect().top, 0, total);
                target = p;

                // Gallery mat: wide white margin -> full bleed -> settle slightly inset
                var grow = map(p, 0, 0.35);        // expand phase
                var settle = map(p, 0.85, 1);      // gentle return of the mat at the end
                var matW = 18 - 18 * grow + 6 * settle;   // vw
                var matV = 26 - 26 * grow + 8 * settle;   // vh
                mat.style.setProperty('--mat', matW + 'vw');
                mat.style.setProperty('--mat-v', matV + 'vh');

                // Phase copy
                setCopy(c1, map(p, 0.06, 0.14) * (1 - map(p, 0.30, 0.38)));
                setCopy(c2, map(p, 0.40, 0.48) * (1 - map(p, 0.62, 0.70)));
                setCopy(c3, map(p, 0.74, 0.82) * (1 - map(p, 0.95, 1)));

                // Altimeter
                altNum.textContent = Math.round(START_ALT * (1 - p));
                altDot.style.top = (p * 100) + '%';
                // Once the mat opens to full bleed the altimeter sits on the film,
                // so flip it to light ink to keep it legible.
                if (alt) {
                    alt.classList.toggle('on-film', grow > 0.55);
                }
                cue.style.opacity = p > 0.04 ? 0 : 1;
            };

            var frame = function () {
                if (dur) {
                    // Lerp toward target for buttery scrubbing.
                    current += (target - current) * 0.12;
                    seekVideo(vid, current * dur);
                }
                requestAnimationFrame(frame);
            };

            window.addEventListener('scroll', function () {
                if (!ticking) {
                    ticking = true;
                    requestAnimationFrame(function () {
                        onScroll();
                        ticking = false;
                    });
                }
            }, {passive: true});

            onScroll();
            requestAnimationFrame(frame);
        }
    }

    /* ---------- 2. Homes by style: pinned horizontal scrub ---------- */

    (function () {
        var scrub = document.querySelector('.scrub');
        if (!scrub) {
            return;
        }
        var stage = scrub.querySelector('.scrub-stage');
        var track = document.getElementById('scrubTrack');
        var prog = scrub.querySelector('.scrub-progress i');
        var progBar = scrub.querySelector('.scrub-progress');
        var isTouch = window.matchMedia('(max-width:900px)').matches;

        /* touch or reduced motion -> plain swipe rail, no pinning */
        if (reducedMotion || isTouch) {
            scrub.classList.add('is-static');
            return;
        }

        var cards = Array.prototype.slice.call(track.children);
        var maxX = 0;
        var trackX = 0;      // current track offset
        var lastX = 0;       // offset last frame, for velocity
        var lag = 0;         // spring position (px the cards trail by)
        var lagV = 0;        // spring velocity
        var running = false;
        var onScreen = false;

        // Spring constants: soft enough to overshoot a little on stop.
        var STIFF = 0.10;
        var DAMP = 0.76;
        var PULL = 0.55;     // how hard scroll velocity drags the cards back
        var MAX_LAG = 46;    // px; keeps fast flicks from colliding neighbours

        function measure() {
            maxX = Math.max(0, track.scrollWidth - stage.clientWidth);
        }

        function readScroll() {
            var total = scrub.offsetHeight - window.innerHeight;
            var scrolled = clamp(-scrub.getBoundingClientRect().top, 0, total);
            var p = total > 0 ? scrolled / total : 0;
            var moveP = map(p, 0.06, 0.94);
            trackX = -moveP * maxX;
            if (prog) {
                prog.style.width = (moveP * 100) + '%';
            }
            if (progBar) {
                progBar.classList.toggle('is-complete', moveP > 0.995);
            }
        }

        function frame() {
            readScroll();
            track.style.transform = 'translateX(' + trackX + 'px)';

            // Velocity of the track this frame; cards are dragged the other way.
            var vel = trackX - lastX;
            lastX = trackX;

            // Critically-ish damped spring pulled toward rest (0), kicked by velocity.
            lagV += (0 - lag) * STIFF;
            lagV *= DAMP;
            lag += lagV - vel * PULL;
            lag = clamp(lag, -MAX_LAG, MAX_LAG);

            for (var i = 0; i < cards.length; i++) {
                // Stagger: later cards trail a touch more, so the row fans out.
                var f = 1 + (i % 3) * 0.28;
                cards[i].style.setProperty('--lag', (lag * f).toFixed(2) + 'px');
            }

            // Keep looping while visible or while the spring is still settling.
            if (onScreen || Math.abs(lag) > 0.05 || Math.abs(lagV) > 0.05) {
                requestAnimationFrame(frame);
            } else {
                running = false;
            }
        }

        function start() {
            if (!running) {
                running = true;
                requestAnimationFrame(frame);
            }
        }

        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function (entries) {
                onScreen = entries[0].isIntersecting;
                if (onScreen) {
                    start();
                }
            }, {threshold: 0}).observe(scrub);
        } else {
            onScreen = true;
            start();
        }

        measure();
        readScroll();
        lastX = trackX;
        track.style.transform = 'translateX(' + trackX + 'px)';

        window.addEventListener('resize', function () {
            measure();
            start();
        });
        window.addEventListener('load', function () {
            measure();
            start();
        });
        window.addEventListener('scroll', start, {passive: true});
    })();

    /* ---------- 2b. Card image parallax (drift within frames) ---------- */

    (function () {
        if (reducedMotion) {
            return;
        }
        var groups = [
            {sel: '.scrub-card img', amp: 14},
            {sel: '.home-card img', amp: 16}
        ];
        var els = [];
        groups.forEach(function (g) {
            Array.prototype.forEach.call(document.querySelectorAll(g.sel), function (el) {
                el._amp = g.amp;
                els.push(el);
            });
        });
        if (!els.length) {
            return;
        }

        function update() {
            var h = window.innerHeight;
            for (var i = 0; i < els.length; i++) {
                var el = els[i];
                var r = el.getBoundingClientRect();
                if (r.bottom < 0 || r.top > h) {
                    continue;
                } // off-screen -> skip
                var center = r.top + r.height / 2;
                var prog = clamp((center - h / 2) / ((h + r.height) / 2), -1, 1); // -1 above · 0 centered · 1 below
                el.style.setProperty('--py', (prog * el._amp).toFixed(1) + 'px');
            }
        }

        update();
        var pp = false;
        window.addEventListener('scroll', function () {
            if (!pp) {
                pp = true;
                requestAnimationFrame(function () {
                    update();
                    pp = false;
                });
            }
        }, {passive: true});
        window.addEventListener('resize', update, {passive: true});
        window.addEventListener('load', update);
    })();

    /* ---------- 3. Reveal on scroll ---------- */

    var revealEls = document.querySelectorAll('[data-reveal]');
    if (revealEls.length && 'IntersectionObserver' in window && !reducedMotion) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, {threshold: 0.18});
        revealEls.forEach(function (el) {
            io.observe(el);
        });
    } else {
        revealEls.forEach(function (el) {
            el.classList.add('is-visible');
        });
    }

    /* Scrub head reveals (prototype-style: blur + rise, adds .in) */
    var scrubReveals = document.querySelectorAll('.scrub .reveal');
    if (scrubReveals.length && 'IntersectionObserver' in window && !reducedMotion) {
        var sio = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in');
                    sio.unobserve(entry.target);
                }
            });
        }, {threshold: 0.16});
        scrubReveals.forEach(function (el) {
            sio.observe(el);
        });
    } else {
        scrubReveals.forEach(function (el) {
            el.classList.add('in');
        });
    }

    /* ---------- 4. Single home gallery lightbox ---------- */

    (function () {
        var gallery = document.querySelector('.sh-gallery');
        if (!gallery) {
            return;
        }
        var photos = Array.prototype.slice.call(gallery.querySelectorAll('.sh-photo[data-full]'));
        if (!photos.length) {
            return;
        }

        var lb = document.createElement('div');
        lb.className = 'sh-lightbox';
        lb.setAttribute('role', 'dialog');
        lb.setAttribute('aria-modal', 'true');
        lb.setAttribute('aria-label', 'Photo viewer');
        lb.innerHTML = '<img alt="">' +
            '<button type="button" class="lb-close">Close</button>' +
            '<button type="button" class="lb-prev" aria-label="Previous photo">&larr;</button>' +
            '<button type="button" class="lb-next" aria-label="Next photo">&rarr;</button>' +
            '<span class="lb-count"></span>';
        document.body.appendChild(lb);

        var img = lb.querySelector('img');
        var count = lb.querySelector('.lb-count');
        var index = 0;
        var lastFocus = null;

        function show(i) {
            index = (i + photos.length) % photos.length;
            img.src = photos[index].getAttribute('data-full');
            var thumb = photos[index].querySelector('img');
            img.alt = thumb ? thumb.alt : '';
            count.textContent = (index + 1) + ' / ' + photos.length;
        }

        function open(i) {
            lastFocus = document.activeElement;
            show(i);
            lb.classList.add('open');
            document.body.classList.add('is-locked');
            lb.querySelector('.lb-close').focus();
        }

        function close() {
            lb.classList.remove('open');
            document.body.classList.remove('is-locked');
            if (lastFocus) {
                lastFocus.focus();
            }
        }

        photos.forEach(function (btn, i) {
            btn.addEventListener('click', function () {
                open(i);
            });
        });

        lb.querySelector('.lb-close').addEventListener('click', close);
        lb.querySelector('.lb-prev').addEventListener('click', function () {
            show(index - 1);
        });
        lb.querySelector('.lb-next').addEventListener('click', function () {
            show(index + 1);
        });
        lb.addEventListener('click', function (e) {
            if (e.target === lb) {
                close();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (!lb.classList.contains('open')) {
                return;
            }
            if (e.key === 'Escape') {
                close();
            }
            if (e.key === 'ArrowLeft') {
                show(index - 1);
            }
            if (e.key === 'ArrowRight') {
                show(index + 1);
            }
        });
    })();

    /* ---------- 5. Our Homes style filter ---------- */

    (function () {
        var grid = document.getElementById('homesGrid');
        var bar = document.getElementById('hmFilters');
        if (!grid) {
            return;
        }
        var cards = Array.prototype.slice.call(grid.querySelectorAll('.home-card'));
        var moreBtn = document.getElementById('hmMore');
        var status = document.getElementById('hmStatus');
        var BATCH = 9;
        var filterVal = '*';
        var revealed = BATCH;

        function slug(s) {
            return String(s).toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        }

        function applyRhythm(shown) {
            cards.forEach(function (c) {
                c.classList.remove('is-wide');
            });
            shown.forEach(function (c, i) {
                if (i % 4 === 0) {
                    c.classList.add('is-wide');
                } // editorial rhythm: wide, 3 standard, wide...
            });
        }

        // Render the grid for the current filter + reveal count.
        // force=true guarantees newly shown cards aren't left transparent
        // (used for filter changes and Load more, where the scroll-reveal
        // observer never fired because the card was display:none).
        function render(force) {
            var matched = cards.filter(function (card) {
                return filterVal === '*' || card.dataset.style === filterVal;
            });
            var shown = matched.slice(0, revealed);
            cards.forEach(function (card) {
                var show = shown.indexOf(card) !== -1;
                if (reducedMotion) {
                    card.classList.toggle('is-hidden', !show);
                    card.classList.remove('is-faded');
                } else if (show) {
                    card.classList.remove('is-hidden');
                    if (force) {
                        card.classList.add('is-visible');
                    }
                    requestAnimationFrame(function () {
                        requestAnimationFrame(function () {
                            card.classList.remove('is-faded');
                        });
                    });
                } else {
                    card.classList.add('is-faded');
                    setTimeout(function () {
                        if (card.classList.contains('is-faded')) {
                            card.classList.add('is-hidden');
                        }
                    }, 320);
                }
            });
            applyRhythm(shown);

            if (moreBtn) {
                var remaining = matched.length - shown.length;
                moreBtn.hidden = remaining <= 0;
                if (remaining > 0) {
                    moreBtn.textContent = 'Load more (' + remaining + ')';
                }
            }
            if (status) {
                var n = matched.length;
                status.textContent = 'Showing ' + Math.min(revealed, n) + ' of ' + n + ' ' +
                    (n === 1 ? 'home' : 'homes') + (filterVal === '*' ? '' : ' in ' + filterVal);
            }
        }

        function setFilter(val) {
            filterVal = val;
            revealed = BATCH;
            render(true);
        }

        function syncURL() {
            if (!window.history || !history.replaceState) {
                return;
            }
            try {
                var url = new URL(window.location.href);
                if (filterVal === '*') {
                    url.searchParams.delete('style');
                } else {
                    url.searchParams.set('style', slug(filterVal));
                }
                history.replaceState(null, '', url);
            } catch (e) {
            }
        }

        // Deep link: apply ?style=<slug> from the URL on load.
        if (bar) {
            try {
                var q = new URL(window.location.href).searchParams.get('style');
                if (q) {
                    Array.prototype.slice.call(bar.querySelectorAll('.hm-filter')).forEach(function (b) {
                        if (b.dataset.filter !== '*' && slug(b.dataset.filter) === q) {
                            bar.querySelectorAll('.hm-filter').forEach(function (o) {
                                o.classList.remove('is-active');
                                o.setAttribute('aria-pressed', 'false');
                            });
                            b.classList.add('is-active');
                            b.setAttribute('aria-pressed', 'true');
                            filterVal = b.dataset.filter;
                        }
                    });
                }
            } catch (e) {
            }
        }

        render(filterVal !== '*');

        if (bar) {
            var canSpring = !reducedMotion && typeof bar.animate === 'function' && 'IntersectionObserver' in window;
            if (canSpring) {
                var pills = Array.prototype.slice.call(bar.querySelectorAll('.hm-filter'));

                // Choreographed toss (motion only; opacity handled separately so these
                // easings stay clean): rise from below, HOLD at the apex, fall under
                // gravity while straightening, one soft overshoot, settle level.
                function tossTrack(tilt) {
                    return [
                        {
                            offset: 0.00,
                            translate: '0 100px',
                            rotate: tilt + 'deg',
                            easing: 'cubic-bezier(0.17, 0.62, 0.34, 1)'
                        },   // rise from 100px below, decelerating
                        {offset: 0.30, translate: '0 -100px', rotate: tilt + 'deg', easing: 'linear'},                              // apex — HANG (no motion)
                        {
                            offset: 0.60,
                            translate: '0 -100px',
                            rotate: tilt + 'deg',
                            easing: 'cubic-bezier(0.55, 0, 0.85, 0.5)'
                        },    // gravity takes over, accelerating
                        {offset: 0.86, translate: '0 0', rotate: '0deg', easing: 'cubic-bezier(0.33, 0.3, 0.5, 1)'},     // land, straightened, easing into the dip
                        {offset: 0.93, translate: '0 12px', rotate: '1.2deg', easing: 'ease-in-out'},                         // soft overshoot below the line
                        {offset: 0.97, translate: '0 -3px', rotate: '-0.4deg', easing: 'ease-in-out'},                         // gentle rebound
                        {offset: 1.00, translate: '0 0', rotate: '0deg'}                                                       // settle level
                    ];
                }

                bar.classList.add('lh-spring-armed'); // hide pills until they toss in

                var fired = false;
                var springIO = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (!entry.isIntersecting || fired) {
                            return;
                        }
                        fired = true;
                        springIO.disconnect();
                        pills.forEach(function (p, i) {
                            var tilt = Math.random() * 90 - 45;   // random tilt -45..45deg
                            var delay = i * 60;                   // left-to-right cascade (~60ms)
                            var opts = {duration: 950, delay: delay, easing: 'linear', fill: 'both'};
                            p.animate(tossTrack(tilt), opts);
                            p.animate([                            // quick fade so the rise is visible
                                {offset: 0, opacity: 0},
                                {offset: 0.13, opacity: 1},
                                {offset: 1, opacity: 1}
                            ], opts);
                        });
                    });
                }, {threshold: 0.25});
                springIO.observe(bar);
            }

            bar.addEventListener('click', function (e) {
                var btn = e.target.closest('.hm-filter');
                if (!btn) {
                    return;
                }
                bar.querySelectorAll('.hm-filter').forEach(function (b) {
                    b.classList.remove('is-active');
                    b.setAttribute('aria-pressed', 'false');
                });
                btn.classList.add('is-active');
                btn.setAttribute('aria-pressed', 'true');
                setFilter(btn.dataset.filter);
                syncURL();
            });
        }

        if (moreBtn) {
            moreBtn.addEventListener('click', function () {
                revealed += BATCH;
                render(true);
            });
        }
    })();

    /* ---------- 6. How We Build: pinned build scrub ---------- */

    (function () {
        var build = document.getElementById('build');
        var vid = document.getElementById('buildVid');
        if (!build || !vid) {
            return;
        }

        var isTouch = window.matchMedia('(max-width:900px)').matches;

        if (reducedMotion || isTouch) {
            build.classList.add('is-static');
            vid.loop = true;
            vid.autoplay = true;
            vid.play().catch(function () {
            });
            return;
        }

        var copies = ['b1', 'b2', 'b3', 'b4'].map(function (id) {
            return document.getElementById(id);
        });
        var stepNum = document.getElementById('bStepNum');
        var railFill = document.getElementById('bRailFill');
        var ticks = Array.prototype.slice.call(build.querySelectorAll('.b-rail .tick'));
        var cue = document.getElementById('bCue');
        var dur = 0, target = 0, current = 0, ticking = false;

        vid.addEventListener('loadedmetadata', function () {
            dur = vid.duration;
            vid.pause();
        });

        function setCopy(el, vis) {
            el.style.opacity = vis;
            el.style.transform = 'translateY(' + ((1 - vis) * 24) + 'px)';
        }

        // Fade windows for the 4 phases along scroll progress p (0..1)
        var windows = [
            [0.05, 0.12, 0.24, 0.30],
            [0.30, 0.37, 0.49, 0.55],
            [0.55, 0.62, 0.74, 0.80],
            [0.80, 0.87, 0.96, 1.00]
        ];

        function onScroll() {
            var total = build.offsetHeight - window.innerHeight;
            var p = map(-build.getBoundingClientRect().top, 0, total);
            target = p;

            copies.forEach(function (el, i) {
                var w = windows[i];
                setCopy(el, map(p, w[0], w[1]) * (1 - map(p, w[2], w[3])));
            });

            var step = clamp(Math.floor(p * 4) + 1, 1, 4);
            stepNum.textContent = '0' + step;
            railFill.style.height = (p * 100) + '%';
            ticks.forEach(function (t, i) {
                t.classList.toggle('is-active', p >= i / 3 - 0.001);
            });
            cue.style.opacity = p > 0.04 ? 0 : 1;
        }

        function frame() {
            if (dur) {
                current += (target - current) * 0.12;
                seekVideo(vid, current * dur);
            }
            requestAnimationFrame(frame);
        }

        window.addEventListener('scroll', function () {
            if (!ticking) {
                ticking = true;
                requestAnimationFrame(function () {
                    onScroll();
                    ticking = false;
                });
            }
        }, {passive: true});

        onScroll();
        requestAnimationFrame(frame);
    })();

    /* ---------- 6b. Nav theme swap (light <-> inverted over dark sections) ---------- */

    (function () {
        var hdr = document.getElementById('site-header');
        if (!hdr) {
            return;
        }

        // Sections whose background is dark: nav flips to its inverted theme
        // while any of them sits behind the floating bar.
        var darkSections = document.querySelectorAll(
            '.descent, .contact, [data-nav="dark"]'
        );
        if (!darkSections.length) {
            return;
        }

        var ticking = false;

        function update() {
            ticking = false;
            // The strip the nav actually occupies (its vertical mid-line).
            var band = hdr.getBoundingClientRect().bottom - hdr.offsetHeight / 2;
            var dark = false;
            for (var i = 0; i < darkSections.length; i++) {
                var r = darkSections[i].getBoundingClientRect();
                if (r.top <= band && r.bottom >= band) {
                    dark = true;
                    break;
                }
            }
            hdr.classList.toggle('is-dark', dark);
        }

        function onScroll() {
            if (!ticking) {
                ticking = true;
                requestAnimationFrame(update);
            }
        }

        window.addEventListener('scroll', onScroll, {passive: true});
        window.addEventListener('resize', onScroll);
        update();
    })();

    /* ---------- 6c. Philosophy: ink scrubs with scroll position ---------- */

    (function () {
        var sec = document.querySelector('.philosophy');
        if (!sec || reducedMotion) {
            return;
        }

        var onScreen = false;
        var ticking = false;

        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function (entries) {
                onScreen = entries[0].isIntersecting;
                if (onScreen) {
                    schedule();
                }
            }, {threshold: 0}).observe(sec);
        } else {
            onScreen = true;
        }

        // Ramp 0->1 across [a,b], clamped.
        function ramp(v, a, b) {
            if (b === a) {
                return 0;
            }
            var t = (v - a) / (b - a);
            return t < 0 ? 0 : (t > 1 ? 1 : t);
        }

        function update() {
            ticking = false;
            if (!onScreen) {
                return;
            }

            var r = sec.getBoundingClientRect();
            var vh = window.innerHeight;
            // 0 as the section enters from below, 1 once it has fully left above.
            var p = (vh - r.top) / (vh + r.height);
            p = p < 0 ? 0 : (p > 1 ? 1 : p);

            // Fill on the way in, drain on the way out. Each word trails the last.
            var f1 = ramp(p, 0.10, 0.32) * (1 - ramp(p, 0.72, 0.90));
            var f2 = ramp(p, 0.16, 0.40) * (1 - ramp(p, 0.76, 0.94));
            var f4 = ramp(p, 0.24, 0.50) * (1 - ramp(p, 0.82, 0.98));

            sec.style.setProperty('--ph-f1', f1.toFixed(3));
            sec.style.setProperty('--ph-f2', f2.toFixed(3));
            sec.style.setProperty('--ph-f4', f4.toFixed(3));
        }

        function schedule() {
            if (!ticking) {
                ticking = true;
                requestAnimationFrame(update);
            }
        }

        window.addEventListener('scroll', schedule, {passive: true});
        window.addEventListener('resize', schedule);
        update();
    })();

    /* ---------- 6d. How we work: play the sequence once, on view ---------- */

    (function () {
        var section = document.getElementById('how-we-work');
        if (!section) {
            return;
        }

        // Measure each thread instead of trusting a hard-coded dasharray: the two
        // paths are ~1581px and ~1579px, so a fixed 1800 overshoots and the lines
        // finish drawing before the animation ends.
        var threads = section.querySelectorAll('.hww-thread');
        for (var i = 0; i < threads.length; i++) {
            if (typeof threads[i].getTotalLength === 'function') {
                var len = Math.ceil(threads[i].getTotalLength());
                threads[i].style.setProperty('--dash', len);
            }
        }

        if (reducedMotion) {
            return;
        }   // CSS already shows the finished drawing

        var started = false;

        function play() {
            if (started) {
                return;
            }
            started = true;
            section.classList.add('is-playing');
            window.setTimeout(function () {
                ['hww-m1', 'hww-m2', 'hww-m3'].forEach(function (id) {
                    var el = document.getElementById(id);
                    if (el && el.beginElement) {
                        try {
                            el.beginElement();
                        } catch (e) { /* SMIL unsupported */
                        }
                    }
                });
            }, 400);  // matches the thread-draw delay
        }

        if ('IntersectionObserver' in window) {
            var io = new IntersectionObserver(function (entries) {
                if (entries[0].isIntersecting) {
                    play();
                    io.disconnect();
                }
            }, {threshold: 0.35});
            io.observe(section);
        } else {
            play();
        }
    })();

    /* ---------- 7. Mobile nav overlay ---------- */

    (function () {
        var toggle = document.getElementById('navToggle');
        var overlay = document.getElementById('navOverlay');
        if (!toggle || !overlay) {
            return;
        }

        var isOpen = false;
        var lastFocus = null;

        function focusables() {
            return [toggle].concat(Array.prototype.slice.call(overlay.querySelectorAll('a[href]')));
        }

        function setOpen(open) {
            isOpen = open;
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            overlay.classList.toggle('open', open);
            document.body.classList.toggle('is-locked', open);
            if (open) {
                lastFocus = document.activeElement;
                var first = overlay.querySelector('a[href]');
                if (first) {
                    first.focus();
                }
            } else if (lastFocus) {
                lastFocus.focus();
            }
        }

        toggle.addEventListener('click', function () {
            setOpen(!isOpen);
        });

        overlay.addEventListener('click', function (e) {
            if (e.target.closest('a[href]')) {
                setOpen(false);
                return;
            }
            if (e.target === overlay) {
                setOpen(false);
            }
        });

        document.addEventListener('keydown', function (e) {
            if (!isOpen) {
                return;
            }
            if (e.key === 'Escape') {
                setOpen(false);
                return;
            }
            if (e.key !== 'Tab') {
                return;
            }
            // Focus trap: cycle between the toggle and the overlay links.
            var items = focusables();
            var i = items.indexOf(document.activeElement);
            if (e.shiftKey) {
                if (i <= 0) {
                    e.preventDefault();
                    items[items.length - 1].focus();
                }
            } else if (i === items.length - 1 || i === -1) {
                e.preventDefault();
                items[0].focus();
            }
        });

        // Close if resized up past the mobile breakpoint.
        window.addEventListener('resize', function () {
            if (isOpen && window.innerWidth > 720) {
                setOpen(false);
            }
        });
    })();

    /* ---------- 8. Contact wizard — "site intake" (si-) ---------- */

    (function () {
        var form = document.getElementById('siWizard');
        if (!form) {
            return;
        }
        form.classList.add('is-js');

        var steps = Array.prototype.slice.call(form.querySelectorAll('.si-step'));
        var curEl = document.getElementById('siCur');
        var fldEl = document.getElementById('siField');
        var fillEl = document.getElementById('siFill');
        var back = document.getElementById('siBack');
        var next = document.getElementById('siNext');
        var send = document.getElementById('siSend');
        var note = document.getElementById('siNote');
        var total = steps.length;
        var current = 0;

        function pad(n) {
            return ('0' + n).slice(-2);
        }

        function setErr(step, msg) {
            var e = step.querySelector('.si-err');
            if (e) {
                e.textContent = msg || '';
            }
        }

        function hasText(step) {
            var ins = step.querySelectorAll('.si-inp');
            for (var k = 0; k < ins.length; k++) {
                if (ins[k].value.trim() !== '') {
                    return true;
                }
            }
            return false;
        }

        function cue(t) {
            if (note.lastChild) {
                note.lastChild.textContent = t;
            }
        }

        function validate(i) {
            var step = steps[i];
            setErr(step, '');
            var reqs = step.querySelectorAll('[data-req]');
            for (var k = 0; k < reqs.length; k++) {
                var el = reqs[k];
                if (el.type === 'radio') {
                    var group = step.querySelectorAll('input[name="' + el.name + '"]');
                    var picked = Array.prototype.some.call(group, function (r) {
                        return r.checked;
                    });
                    if (!picked) {
                        setErr(step, 'Please choose an option.');
                        return false;
                    }
                } else if (!el.value.trim() || !el.checkValidity()) {
                    setErr(step, el.type === 'email' ? 'Please enter a valid email.' : 'Please fill this in.');
                    el.focus();
                    return false;
                }
            }
            return true;
        }

        function refreshNav() {
            var step = steps[current];
            var auto = step.getAttribute('data-auto') === '1';
            var optional = step.getAttribute('data-optional') === '1';
            var last = current === total - 1;
            back.hidden = current === 0;
            if (auto) {
                next.hidden = true;
                send.hidden = true;
                note.style.display = 'flex';
                cue('Select an answer to continue');
            } else if (last) {
                next.hidden = true;
                send.hidden = false;
                note.style.display = 'none';
            } else if (optional) {
                next.hidden = false;
                send.hidden = true;
                note.style.display = 'none';
            } else {
                var t = hasText(step);
                next.hidden = !t;
                send.hidden = true;
                note.style.display = t ? 'none' : 'flex';
                cue('Type your answer to continue');
            }
        }

        function show(i, dir) {
            steps[current].classList.remove('is-active', 'si-fwd', 'si-bwd');
            current = i;
            var step = steps[current];
            step.classList.add('is-active', dir < 0 ? 'si-bwd' : 'si-fwd');
            curEl.textContent = pad(current + 1);
            fldEl.textContent = step.getAttribute('data-field') || '';
            fillEl.style.width = ((current + 1) / total * 100) + '%';
            refreshNav();
            var focusable = step.querySelector('.si-inp');
            if (focusable) {
                setTimeout(function () {
                    focusable.focus();
                }, 260);
            }
        }

        function advance() {
            if (!validate(current)) {
                return;
            }
            if (current < total - 1) {
                show(current + 1, 1);
            } else {
                form.submit();
            }
        }

        /* Option cards: mark selection, auto-advance on choice steps */
        form.addEventListener('change', function (e) {
            if (e.target.type !== 'radio') {
                return;
            }
            var group = form.querySelectorAll('input[name="' + e.target.name + '"]');
            group.forEach(function (r) {
                r.closest('.si-opt').classList.toggle('is-sel', r.checked);
            });
            var step = e.target.closest('.si-step');
            if (step && step.getAttribute('data-auto') === '1') {
                setTimeout(function () {
                    advance();
                }, 340);
            }
        });

        /* Typed steps: reveal Continue once they start typing */
        form.addEventListener('input', function (e) {
            if (!e.target.classList.contains('si-inp')) {
                return;
            }
            var step = steps[current];
            if (step.getAttribute('data-auto') === '1' || step.getAttribute('data-optional') === '1' || current === total - 1) {
                return;
            }
            var t = hasText(step);
            next.hidden = !t;
            note.style.display = t ? 'none' : 'flex';
        });

        next.addEventListener('click', advance);
        back.addEventListener('click', function () {
            show(Math.max(0, current - 1), -1);
        });

        /* Enter advances instead of submitting mid-flow (textarea keeps newlines) */
        form.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter' || e.target.tagName === 'TEXTAREA') {
                return;
            }
            e.preventDefault();
            advance();
        });

        /* Full validation on submit; jump to first invalid step */
        form.addEventListener('submit', function (e) {
            for (var i = 0; i < total; i++) {
                if (!validate(i)) {
                    if (i !== current) {
                        show(i, i < current ? -1 : 1);
                    }
                    e.preventDefault();
                    return;
                }
            }
        });

        show(0, 1);
    })();

    /* ---------- Start where you are: tap-to-flip on touch ---------- */
    (function () {
        var cards = Array.prototype.slice.call(document.querySelectorAll('.ways-card'));
        if (!cards.length) {
            return;
        }
        if (window.matchMedia('(hover: hover)').matches) {
            return;
        } // desktop uses CSS hover
        cards.forEach(function (card) {
            card.addEventListener('click', function (e) {
                if (!card.classList.contains('is-flipped')) {
                    e.preventDefault(); // first tap flips; second tap follows the link
                    cards.forEach(function (c) {
                        if (c !== card) {
                            c.classList.remove('is-flipped');
                        }
                    });
                    card.classList.add('is-flipped');
                }
            });
        });
    })();
})();