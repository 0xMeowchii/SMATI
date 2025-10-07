<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../Teacher/teacher.css">
</head>

<body>
    <!-- Sidebar -->
    <?php include('sidebar.php'); ?>

    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title"><i class="fas fa-tachometer-alt me-2"></i>Dashboard Overview</h1>
        </div>

        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center fw-bold bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Concerns</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Reference #</th>
                                <th>Name</th>
                                <th>Section</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>

</html>