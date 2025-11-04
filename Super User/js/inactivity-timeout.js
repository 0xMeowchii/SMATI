class InactivityTimeout {
    constructor(options = {}) {
        // Configurable options
        this.inactivityTime = options.inactivityTime || 5 * 60 * 1000; // 5 minutes default
        this.warningTime = options.warningTime || 30 * 1000; // 30 seconds warning
        this.logoutUrl = options.logoutUrl || 'includes/logout.php';
        
        this.inactivityTimer = null;
        this.warningTimer = null;
        this.countdownInterval = null;
        this.isWarningShown = false;
        
        this.init();
    }
    
    init() {
        // Events that reset the timer
        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
        
        events.forEach(event => {
            document.addEventListener(event, () => this.resetTimer(), true);
        });
        
        // Start the timer
        this.resetTimer();
    }
    
    resetTimer() {
        // FIXED: Don't reset if warning is currently shown
        // Let the user make their choice instead
        if (this.isWarningShown) {
            return;
        }
        
        // Clear existing timers
        clearTimeout(this.inactivityTimer);
        clearTimeout(this.warningTimer);
        
        // Set new timers
        this.warningTimer = setTimeout(() => this.showWarning(), this.inactivityTime - this.warningTime);
        this.inactivityTimer = setTimeout(() => this.logout(), this.inactivityTime);
    }
    
    showWarning() {
        this.isWarningShown = true;
        let remainingSeconds = Math.floor(this.warningTime / 1000);
        
        Swal.fire({
            title: 'Inactive Session',
            html: `You've been inactive for 5 minutes.<br>You will be logged out in <strong>${remainingSeconds}</strong> seconds.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Stay Logged In',
            cancelButtonText: 'Logout Now',
            allowOutsideClick: false,
            allowEscapeKey: false,
            timer: this.warningTime,
            timerProgressBar: true,
            didOpen: () => {
                // Update countdown every second
                this.countdownInterval = setInterval(() => {
                    remainingSeconds--;
                    if (remainingSeconds > 0) {
                        Swal.getHtmlContainer().querySelector('strong').textContent = remainingSeconds;
                    }
                }, 1000);
            },
            willClose: () => {
                clearInterval(this.countdownInterval);
                this.isWarningShown = false; // Reset flag when modal closes
            }
        }).then((result) => {
            this.isWarningShown = false; // Ensure flag is reset
            
            if (result.isConfirmed) {
                // User clicked "Stay Logged In"
                this.resetTimer();
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                // User clicked "Logout Now"
                this.logout();
            } else {
                // Timer ran out
                this.logout();
            }
        });
    }
    
    logout() {
        // Clear all timers to prevent any issues
        clearTimeout(this.inactivityTimer);
        clearTimeout(this.warningTimer);
        clearInterval(this.countdownInterval);
        
        Swal.fire({
            title: 'Session Expired',
            text: 'You have been logged out due to inactivity.',
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            }
        }).then(() => {
            window.location.href = this.logoutUrl;
        });
    }
}