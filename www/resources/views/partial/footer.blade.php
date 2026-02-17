    @fluxScripts
    @livewireScripts
    @livewireScriptConfig
    <script>
        if (!window._spinnerInitialized) {
            window._spinnerInitialized = true;

            document.addEventListener('livewire:navigating', () => {
                const spinner = document.getElementById('page-spinner');
                console.log('navigating - spinner element:', spinner);
                if (spinner) spinner.style.display = 'flex';
            });

            document.addEventListener('livewire:navigated', () => {
                const spinner = document.getElementById('page-spinner');
                console.log('navigated - spinner element:', spinner);
                if (spinner) spinner.style.display = 'none';
            });
        }
    </script>

    </body>

    </html>
