<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        <script async src="https://www.googletagmanager.com/gtag/js?id=G-X2X7MWLP22"></script>

        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'G-X2X7MWLP22');

            window.setTheme = (theme) => {
                const root = document.documentElement;
                root.className = '';

                if (theme && theme !== 'dark') {
                    root.classList.add(theme);
                    localStorage.setItem('theme', theme);
                } else {
                    localStorage.removeItem('theme');
                }
            };
            window.setTheme(localStorage.getItem('theme'));
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="bg-base text-main min-h-screen flex flex-col">

        <x-header />
        <main class="container flex-1 mx-auto px-4 py-6 flex">
            <canvas id="spaceCanvas" class="fixed inset-0 w-full h-full pointer-events-none z-[-1]"></canvas>
            {{ $slot }}
        </main>
        <x-footer/>
        <x-toaster-hub />

        @livewireScripts
        <script>

           document.addEventListener('alpine:init', (e) => {
               //AI Generated
               Alpine.data('starfield', () => ({
                   boost: 0,
                   stars: [],
                   colors: { bg: [0, 0, 0], star: '#ffffff' },
                   ctx: null,
                   W: 0,
                   H: 0,
                   dpr: Math.min(window.devicePixelRatio || 1, 2),
                   reducedMotion: window.matchMedia('(prefers-reduced-motion: reduce)').matches,

                   init() {
                       const cv = document.getElementById('spaceCanvas');
                       if (!cv) return;
                       this.ctx = cv.getContext('2d');

                       // Read colors & observe theme changes
                       const observer = new MutationObserver(() => this.updateColors());
                       observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
                       observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });

                       // Resize canvas dynamically on window resize
                       window.addEventListener('resize', () => this.size(cv));

                       // Boost warp speed on user typing
                       window.addEventListener('keydown', (e) => {
                           if (this.reducedMotion) return;
                           this.boost = e.key === 'Enter' ? 1 : Math.min(1, this.boost + 0.12);
                       });

                       this.size(cv);
                       this.frame(cv);
                   },

                   rgb(hex) {
                       const tempCtx = document.createElement('canvas').getContext('2d');
                       tempCtx.fillStyle = '#000';
                       tempCtx.fillStyle = hex;
                       const normalizedHex = tempCtx.fillStyle;
                       return [1, 3, 5].map(i => parseInt(normalizedHex.slice(i, i + 2), 16));
                   },

                   updateColors() {
                       const styles = getComputedStyle(document.body);

                       const bgVal = styles.getPropertyValue('--brand-bg-base').trim() || '#000000';
                       const starVal = styles.getPropertyValue('--brand-primary').trim() || '#ffffff';

                       this.colors.bg = this.rgb(bgVal);
                       this.colors.star = starVal;
                   },

                   resetStars() {
                       this.stars = Array.from({ length: 180 }, () => ({
                           x: (Math.random() - 0.5) * 2,
                           y: (Math.random() - 0.5) * 2,
                           z: Math.random() * 0.95 + 0.05
                       }));
                   },

                   size(cv) {
                       this.W = window.innerWidth;
                       this.H = window.innerHeight;

                       cv.width = this.W * this.dpr;
                       cv.height = this.H * this.dpr;

                       this.ctx.setTransform(this.dpr, 0, 0, this.dpr, 0, 0);
                       this.updateColors();
                       this.resetStars();
                   },

                   frame(cv) {
                       this.boost *= 0.95;

                       // Draw semi-transparent background trail
                       const [r, g, b] = this.colors.bg;
                       this.ctx.fillStyle = `rgba(${r}, ${g}, ${b}, 0.35)`;
                       this.ctx.fillRect(0, 0, this.W, this.H);

                       const cx = this.W / 2;
                       const cy = this.H / 2;
                       const speed = (0.0035 + this.boost * 0.05) * (this.reducedMotion ? 0.3 : 1);

                       this.ctx.lineCap = 'round';
                       this.ctx.strokeStyle = this.colors.star;

                       for (const s of this.stars) {
                           s.z -= speed;
                           if (s.z < 0.03) {
                               s.x = (Math.random() - 0.5) * 2;
                               s.y = (Math.random() - 0.5) * 2;
                               s.z = 1;
                           }

                           const sx = cx + (s.x / s.z) * cx * 0.4;
                           const sy = cy + (s.y / s.z) * cy * 0.4;

                           const t = s.z + speed * 3;
                           const px = cx + (s.x / t) * cx * 0.4;
                           const py = cy + (s.y / t) * cy * 0.4;

                           if (sx < 0 || sx > this.W || sy < 0 || sy > this.H) {
                               s.z = 1;
                               continue;
                           }

                           this.ctx.lineWidth = 0.8 + (1 - s.z) * 1.8;
                           this.ctx.beginPath();
                           this.ctx.moveTo(px, py);
                           this.ctx.lineTo(sx, sy);
                           this.ctx.stroke();
                       }

                       requestAnimationFrame(() => this.frame(cv));
                   }
               }));
           });

        </script>
        <div x-data="starfield" class="hidden"></div>
    </body>
</html>
