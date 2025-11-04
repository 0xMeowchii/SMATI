<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SMATI - Grading Portal</title>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="./css/sidebar.css">
<link rel="stylesheet" href="./css/darkmode-variable.css">
<link rel="stylesheet" href="./css/admin.css">
<link rel="icon" type="image/png" href="../images/logo5.png">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="js/inactivity-timeout1.js"></script>
<script>
    // This ensures it runs after page loads
    document.addEventListener('DOMContentLoaded', function() {
        const timeout = new InactivityTimeout({
            logoutUrl: 'includes/logout.php' // Change per user type
        });
    });
</script>