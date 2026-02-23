{{-- Skip on subsequent page visits --}}
<script>
    if (sessionStorage.getItem('bcu_lms_loaded')) {
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.getElementById('bcu-loading-screen');
            if (el) el.style.display = 'none';
        });
    }
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Cormorant+Garamond:wght@300;500&family=EB+Garamond:ital,wght@0,400;1,400&display=swap');

    /* ── Animations ───────────────────────────────────────────── */
    @keyframes bcu-slide-up  { from { opacity:0; transform:translateY(12px) } to { opacity:1; transform:translateY(0) } }

    @keyframes bcu-ring-glow {
        0%,100% { box-shadow: 0 0 28px rgba(201,168,76,.12), inset 0 0 18px rgba(0,0,0,.35); }
        50%      { box-shadow: 0 0 48px rgba(201,168,76,.28), inset 0 0 18px rgba(0,0,0,.35); }
    }
    @keyframes bcu-pulse-ring {
        0%   { transform:scale(1);   opacity:.22; }
        70%  { transform:scale(1.6); opacity:0;   }
        100% { transform:scale(1.6); opacity:0;   }
    }
    @keyframes bcu-spin {
        from { transform:rotate(0deg) }
        to   { transform:rotate(360deg) }
    }
    @keyframes bcu-step-in {
        from { opacity:0; transform:translateX(8px); }
        to   { opacity:1; transform:translateX(0);   }
    }

    /* ── Entrance animations ───────────────────────────────────── */
    #bcu-loading-screen .bcu-emblem     { animation: bcu-slide-up .9s ease .1s both; }
    #bcu-loading-screen .bcu-wordmark   { animation: bcu-slide-up .9s ease .3s both; }
    #bcu-loading-screen .bcu-divider    { animation: bcu-slide-up .9s ease .45s both; }
    #bcu-loading-screen .bcu-subtitle   { animation: bcu-slide-up .9s ease .55s both; }

    /* ── Emblem ────────────────────────────────────────────────── */
    .bcu-emblem-ring {
        width: 100px; height: 100px; border-radius: 50%;
        border: 1.5px solid #c9a84c;
        background: radial-gradient(circle, rgba(92,5,3,.85) 0%, rgba(26,3,2,.97) 100%);
        display: flex; align-items: center; justify-content: center;
        position: relative;
        animation: bcu-ring-glow 3s ease-in-out infinite;
    }
    .bcu-emblem-ring::before {
        content:'';
        position:absolute; inset:6px;
        border-radius:50%;
        border:1px solid rgba(201,168,76,.25);
    }
    .bcu-pulse-ring {
        position:absolute; inset:0; border-radius:50%;
        background:#860805;
        animation: bcu-pulse-ring 2.2s ease-out infinite;
    }

    /* ── Spinner ───────────────────────────────────────────────── */
    .bcu-spin { animation: bcu-spin .8s linear infinite; }

    /* ── Step row ──────────────────────────────────────────────── */
    .bcu-step-row { animation: bcu-step-in .3s ease both; }

    /* ── Progress shimmer ──────────────────────────────────────── */
    @keyframes bcu-shimmer {
        0%   { background-position: 200% 0; }
        100% { background-position:-200% 0; }
    }
    .bcu-progress-fill {
        height:100%; border-radius:9999px;
        background: linear-gradient(90deg, #860805, #c9a84c, #860805);
        background-size: 200% 100%;
        transition: width .5s ease;
        animation: bcu-shimmer 2s linear infinite;
    }

    /* ── Fade-out ──────────────────────────────────────────────── */
    #bcu-loading-screen { transition: opacity .7s ease; }
    #bcu-loading-screen.bcu-fade-out { opacity:0; pointer-events:none; }
</style>

<div
    id="bcu-loading-screen"
    x-data="bcuLoadingScreen()"
    style="
        position: fixed; inset: 0; z-index: 99999;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        background-color: #1a0302;
        overflow: hidden;
        font-family: 'EB Garamond', Georgia, serif;
    "
>

    {{-- ── Layered radial background ─────────────────────────── --}}
    <div style="
        position:absolute; inset:0; pointer-events:none;
        background:
            radial-gradient(ellipse at 18% 50%, rgba(201,168,76,.06) 0%, transparent 60%),
            radial-gradient(ellipse at 80% 20%, rgba(134,8,5,.55)   0%, transparent 50%),
            radial-gradient(ellipse at 60% 85%, rgba(92,5,3,.7)     0%, transparent 50%);
    "></div>

    {{-- ── Ledger grid ─────────────────────────────────────────── --}}
    <div style="
        position:absolute; inset:0; pointer-events:none;
        background-image:
            linear-gradient(rgba(201,168,76,.035) 1px, transparent 1px),
            linear-gradient(90deg, rgba(201,168,76,.035) 1px, transparent 1px);
        background-size: 40px 40px;
    "></div>

    {{-- ── Horizontal rules ────────────────────────────────────── --}}
    <div style="position:absolute;top:26px;left:100px;right:100px;height:1px;background:linear-gradient(90deg,transparent,rgba(201,168,76,.4),transparent);"></div>
    <div style="position:absolute;bottom:26px;left:100px;right:100px;height:1px;background:linear-gradient(90deg,transparent,rgba(201,168,76,.4),transparent);"></div>

    {{-- ── Corner SVG ornaments ────────────────────────────────── --}}
    @foreach([
        ['top:14px;left:14px;',                         ''],
        ['top:14px;right:14px;transform:scaleX(-1);',   ''],
        ['bottom:14px;left:14px;transform:scaleY(-1);', ''],
        ['bottom:14px;right:14px;transform:scale(-1,-1);', ''],
    ] as $corner)
    <div style="position:absolute;width:72px;height:72px;opacity:.32;{{ $corner[0] }}">
        <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:100%;height:100%;">
            <path d="M4 76 L4 4 L76 4" stroke="#c9a84c" stroke-width="1.5" fill="none"/>
            <path d="M4 20 L20 4"       stroke="#c9a84c" stroke-width=".8"  fill="none" opacity=".5"/>
            <path d="M4 36 L36 4"       stroke="#c9a84c" stroke-width=".8"  fill="none" opacity=".3"/>
            <circle cx="4" cy="4" r="3" fill="#c9a84c" opacity=".8"/>
        </svg>
    </div>
    @endforeach

    <div style="position:relative;z-index:10;display:flex;flex-direction:column;align-items:center;width:100%;max-width:26rem;padding:0 2rem;">

        <div class="bcu-emblem" style="position:relative;margin-bottom:1.6rem;">
            <div class="bcu-emblem-ring">
                <div class="bcu-pulse-ring"></div>
                <img
                    src="{{ asset('favicon.ico') }}"
                    alt="BCU"
                    style="width:100%;height:100%;object-fit:contain;border-radius:9999px;position:relative;z-index:1;"
                >
            </div>
        </div>

        <div class="bcu-wordmark" style="text-align:center;">
            <div style="
                font-family:'Playfair Display',Georgia,serif;
                font-size:1.25rem;font-weight:700;
                color:#eae0d2;letter-spacing:.09em;
                text-shadow:0 2px 14px rgba(0,0,0,.55);
                line-height:1.25;
            ">Baguio Central University</div>
        </div>

        <div class="bcu-divider" style="display:flex;align-items:center;gap:10px;margin:1rem 0;">
            <div style="width:52px;height:1px;background:linear-gradient(90deg,transparent,rgba(201,168,76,.7));"></div>
            <div style="width:6px;height:6px;background:#c9a84c;transform:rotate(45deg);opacity:.85;"></div>
            <div style="width:52px;height:1px;background:linear-gradient(90deg,rgba(201,168,76,.7),transparent);"></div>
        </div>

        <div class="bcu-subtitle" style="
            font-family:'Cormorant Garamond',Georgia,serif;
            font-size:.72rem;font-weight:500;
            color:#e2c97e;letter-spacing:.28em;text-transform:uppercase;text-align:center;
        ">Library Management System</div>

        <div class="bcu-steps-wrap" style="width:100%;margin-top:2rem;">

            <div style="display:flex;flex-direction:column;gap:.55rem;margin-bottom:1.4rem;">
                <template x-for="(step, index) in steps" :key="index">
                    <div
                        class="bcu-step-row"
                        x-show="index === currentStep"
                        style="display:flex;align-items:center;gap:.7rem;"
                    >
                        <div style="width:1rem;height:1rem;flex-shrink:0;display:flex;align-items:center;justify-content:center;">

                            <svg
                                x-show="index === currentStep && allDone"
                                viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="2.5"
                                stroke-linecap="round" stroke-linejoin="round"
                                style="width:1rem;height:1rem;"
                            >
                                <path d="M5 13l4 4L19 7"/>
                            </svg>

                            <svg
                                x-show="index === currentStep && !allDone"
                                class="bcu-spin"
                                viewBox="0 0 24 24"
                                style="width:1rem;height:1rem;"
                            >
                                <circle cx="12" cy="12" r="10" stroke="rgba(234,224,210,.18)" stroke-width="4" fill="none"/>
                                <path fill="#eae0d2" opacity=".75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>

                        </div>

                        <span
                            x-text="step.label"
                            :style="{
                                color: (index === currentStep && allDone)
                                        ? 'rgba(201,168,76,.85)'
                                        : '#eae0d2',
                                fontFamily: 'monospace',
                                fontSize: '.68rem',
                                letterSpacing: '.04em',
                                transition: 'color .3s ease'
                            }"
                        ></span>
                    </div>
                </template>
            </div>

            <div style="width:100%;height:2px;border-radius:9999px;background:rgba(234,224,210,.1);overflow:hidden;margin-bottom:.45rem;">
                <div
                    class="bcu-progress-fill"
                    :style="{ width: progressPercent + '%' }"
                ></div>
            </div>

            <p
                x-text="Math.round(progressPercent) + '%'"
                style="color:rgba(234,224,210,.3);font-family:monospace;font-size:.62rem;letter-spacing:.12em;text-align:right;"
            ></p>

        </div>
    </div>

    <div style="position:absolute;bottom:.9rem;text-align:center;">
        <p style="color:rgba(234,224,210,.1);font-family:monospace;font-size:.58rem;letter-spacing:.14em;">
            LMIS v1.0 &nbsp;&middot;&nbsp; {{ now()->year }}
        </p>
    </div>

</div>

<script>
    function bcuLoadingScreen() {
        return {
            currentStep: 0,
            allDone: false,

            steps: [
                { label: 'Initializing application kernel...',  duration: 420 },
                { label: 'Connecting to local database...',     duration: 580 },
                { label: 'Loading service providers...',        duration: 360 },
                { label: 'Registering route middleware...',     duration: 300 },
                { label: 'Bootstrapping Livewire runtime...',   duration: 450 },
                { label: 'Mounting UI components...',           duration: 380 },
                { label: 'Verifying session integrity...',      duration: 320 },
                { label: 'System ready.',                       duration: 250 },
            ],

            get progressPercent() {
                if (this.allDone) return 100;
                return (this.currentStep / this.steps.length) * 100;
            },

            async init() {
                if (sessionStorage.getItem('bcu_lms_loaded')) {
                    this.$el.style.display = 'none';
                    return;
                }

                for (let i = 0; i < this.steps.length; i++) {
                    this.currentStep = i;
                    await new Promise(r => setTimeout(r, this.steps[i].duration));
                }

                this.allDone = true;
                await new Promise(r => setTimeout(r, 600));

                this.$el.classList.add('bcu-fade-out');
                await new Promise(r => setTimeout(r, 700));

                this.$el.style.display = 'none';
                sessionStorage.setItem('bcu_lms_loaded', '1');
            }
        }
    }
</script>
