<!-- Global Loader -->
<div id="global-loader">
    <div class="loader-spinner"></div>
</div>

<style>
    /* Global Loader Styles */
    #global-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(255, 255, 255, 0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 999999;
        visibility: hidden;
        opacity: 0;
        transition: opacity 0.2s ease, visibility 0.2s ease;
        backdrop-filter: blur(2px);
    }
    #global-loader.show {
        visibility: visible;
        opacity: 1;
    }
    .loader-spinner {
        width: 60px;
        height: 60px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid var(--accent, #0d6efd);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form');
        const loader = document.getElementById('global-loader');

        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                // Ignore forms with target="_blank"
                if (this.target === '_blank') return;
                
                // Do not show loader for specific excluded forms
                if (this.classList.contains('no-loader')) return;

                // Basic HTML5 validation check
                if (this.checkValidity()) {
                    // Prevent multiple clicks on the submit button
                    const submitBtns = this.querySelectorAll('button[type="submit"], input[type="submit"]');
                    submitBtns.forEach(btn => {
                        // Prevent rapid double clicks
                        setTimeout(() => {
                            btn.disabled = true;
                            if (btn.tagName === 'BUTTON') {
                                btn.dataset.originalText = btn.innerHTML;
                                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
                            }
                        }, 10);
                    });
                    
                    // Show the full page loader
                    loader.classList.add('show');
                }
            });
        });

        // Handle page load event (hide loader when user navigates back using browser history)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                loader.classList.remove('show');
                const forms = document.querySelectorAll('form');
                forms.forEach(form => {
                    const submitBtns = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                    submitBtns.forEach(btn => {
                        btn.disabled = false;
                        if (btn.tagName === 'BUTTON' && btn.dataset.originalText) {
                            btn.innerHTML = btn.dataset.originalText;
                        }
                    });
                });
            }
        });
    });
</script>
