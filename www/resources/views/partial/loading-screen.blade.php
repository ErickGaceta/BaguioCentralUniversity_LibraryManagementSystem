<script>
    if (sessionStorage.getItem('bcu_lms_loaded')) {
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.getElementById('bcu-loading-screen');
            if (el) el.style.display = 'none';
        });
    }
</script>

<div
    id="bcu-loading-screen"
    x-data="loadingScreen()"
    x-ref="screen"
    style="
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        width: 100vw;
        height: 100vh;
        z-index: 99999;
        background-color: #1a0302;
    "
>
    {{-- Corner accents --}}
    <div style="position:absolute;top:1.5rem;left:1.5rem;width:3rem;height:3rem;border-top:2px solid rgba(234,224,210,0.2);border-left:2px solid rgba(234,224,210,0.2);"></div>
    <div style="position:absolute;top:1.5rem;right:1.5rem;width:3rem;height:3rem;border-top:2px solid rgba(234,224,210,0.2);border-right:2px solid rgba(234,224,210,0.2);"></div>
    <div style="position:absolute;bottom:1.5rem;left:1.5rem;width:3rem;height:3rem;border-bottom:2px solid rgba(234,224,210,0.2);border-left:2px solid rgba(234,224,210,0.2);"></div>
    <div style="position:absolute;bottom:1.5rem;right:1.5rem;width:3rem;height:3rem;border-bottom:2px solid rgba(234,224,210,0.2);border-right:2px solid rgba(234,224,210,0.2);"></div>

    {{-- Content wrapper --}}
    <div style="display:flex;flex-direction:column;align-items:center;width:100%;max-width:22rem;padding:0 2rem;">

        {{-- Logo with pulse ring --}}
        <div style="position:relative;margin-bottom:1.5rem;">
            <div style="
                width:5rem;height:5rem;border-radius:9999px;
                display:flex;align-items:center;justify-content:center;
                background-color:#860805;
                box-shadow:0 0 50px rgba(134,8,5,0.6);
            ">
                <img src="{{ asset('favicon.ico') }}" alt="BCU" style="width:3rem;height:3rem;object-fit:contain;border-radius:9999px;">
            </div>
            <div class="bcu-pulse-ring"></div>
        </div>

        {{-- Institution name --}}
        <p style="color:#860805;font-family:Georgia,serif;font-size:0.7rem;font-weight:700;letter-spacing:0.25em;text-transform:uppercase;margin-bottom:0.25rem;text-align:center;">
            Baguio Central University
        </p>
        <h1 style="color:#eae0d2;font-family:Georgia,serif;font-size:1.1rem;font-weight:700;letter-spacing:0.15em;text-transform:uppercase;text-align:center;">
            Library System
        </h1>

        {{-- Divider --}}
        <div style="width:6rem;height:1px;background-color:rgba(234,224,210,0.25);margin:1.25rem 0;"></div>

        {{-- Steps list --}}
        <div style="width:100%;display:flex;flex-direction:column;gap:0.6rem;margin-bottom:1.5rem;">
            <template x-for="(step, index) in steps" :key="index">
                <div
                    style="display:flex;align-items:center;gap:0.75rem;"
                    x-show="index <= currentStep"
                    x-transition:enter="transition-all duration-300 ease-out"
                    x-transition:enter-start="opacity-0 translate-x-2"
                    x-transition:enter-end="opacity-100 translate-x-0"
                >
                    {{-- Icon slot --}}
                    <div style="width:1rem;height:1rem;flex-shrink:0;display:flex;align-items:center;justify-content:center;">

                        <svg x-show="index < currentStep || (index === currentStep && allDone)"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
                             style="color:#860805;width:1rem;height:1rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>

                        <svg x-show="index === currentStep && !allDone"
                             fill="none" viewBox="0 0 24 24"
                             class="bcu-spin"
                             style="color:#eae0d2;width:1rem;height:1rem;">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>

                    </div>

                    {{-- Label --}}
                    <span
                        x-text="step.label"
                        :style="{
                            color: (index < currentStep || (index === currentStep && allDone))
                                   ? 'rgba(134,8,5,0.9)'
                                   : index === currentStep
                                   ? '#eae0d2'
                                   : 'rgba(234,224,210,0.25)',
                            fontFamily: 'monospace',
                            fontSize: '0.7rem',
                            letterSpacing: '0.04em',
                            transition: 'color 0.3s ease'
                        }"
                    ></span>
                </div>
            </template>
        </div>

        {{-- Progress bar track --}}
        <div style="width:100%;height:2px;border-radius:9999px;background-color:rgba(234,224,210,0.1);overflow:hidden;margin-bottom:0.5rem;">
            <div
                style="height:100%;border-radius:9999px;background:linear-gradient(90deg,#860805,#c41208);transition:width 0.5s ease;"
                :style="{ width: progressPercent + '%' }"
            ></div>
        </div>

        {{-- Percent --}}
        <p x-text="Math.round(progressPercent) + '%'"
           style="color:rgba(234,224,210,0.35);font-family:monospace;font-size:0.65rem;letter-spacing:0.1em;"></p>

    </div>

    {{-- Version stamp --}}
    <div style="position:absolute;bottom:1rem;text-align:center;">
        <p style="color:rgba(234,224,210,0.12);font-family:monospace;font-size:0.6rem;letter-spacing:0.12em;">
            LMIS v1.0 &nbsp;&middot;&nbsp; {{ now()->year }}
        </p>
    </div>

</div>

<style>
    @keyframes bcu-spin {
        from { transform: rotate(0deg); }
        to   { transform: rotate(360deg); }
    }
    .bcu-spin { animation: bcu-spin 0.8s linear infinite; }

    @keyframes bcu-pulse-ring {
        0%   { transform: scale(1);   opacity: 0.25; }
        70%  { transform: scale(1.5); opacity: 0; }
        100% { transform: scale(1.5); opacity: 0; }
    }
    .bcu-pulse-ring {
        position: absolute;
        inset: 0;
        border-radius: 9999px;
        background-color: #860805;
        animation: bcu-pulse-ring 2s ease-out infinite;
    }

    #bcu-loading-screen {
        transition: opacity 0.7s ease;
    }
    #bcu-loading-screen.bcu-fade-out {
        opacity: 0;
        pointer-events: none;
    }
</style>

<script>
    function loadingScreen() {
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
                // Already visited — hide immediately (inline script above may have already
                // hidden it, but this is a safety net for when Alpine boots faster)
                if (sessionStorage.getItem('bcu_lms_loaded')) {
                    this.$el.style.display = 'none';
                    return;
                }

                // Walk through steps
                for (let i = 0; i < this.steps.length; i++) {
                    this.currentStep = i;
                    await new Promise(r => setTimeout(r, this.steps[i].duration));
                }

                this.allDone = true;
                await new Promise(r => setTimeout(r, 600));

                // Fade out
                this.$el.classList.add('bcu-fade-out');
                await new Promise(r => setTimeout(r, 700));

                // Remove from DOM entirely
                this.$el.style.display = 'none';
                sessionStorage.setItem('bcu_lms_loaded', '1');
            }
        }
    }
</script>
