<?php
include '../database.php';

//ACTIVE STUDENTS
$conn = connectToDB();
$sql = "SELECT * FROM students WHERE status = '1'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$activeStudents = $result->num_rows;

//ACTIVE TEACHERS
$conn = connectToDB();
$sql = "SELECT * FROM teachers WHERE status = '1'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$activeTeachers = $result->num_rows;

//SET A
$conn = connectToDB();
$sql = "SELECT * FROM students WHERE status = '1' AND course = 'A'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$setA = $result->num_rows;

//SET B
$conn = connectToDB();
$sql = "SELECT * FROM students WHERE status = '1' AND course = 'B'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$setB = $result->num_rows;

?>

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
    <link rel="stylesheet" href="../Admin/admin.css">
</head>

<body>
    <!-- Sidebar -->
    <?php include('sidebar.php'); ?>

    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title"><i class="fas fa-tachometer-alt me-2"></i>Dashboard Overview</h1>
        </div>

        <div class="row">
            <div class="col-md-6 col-lg-3">
                <div class="card">
                    <div class="card-body border-primary border-start border-5 rounded shadow-sm">
                        <h6 class="card-subtitle mb-2">Active Students</h6>
                        <i class="fas fa-user fs-1 opacity-25 position-absolute top-0 end-0 mt-3 me-3"></i>
                        <h2 class="mb-3"><?php echo $activeStudents ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card">
                    <div class="card-body border-success border-start border-5 rounded shadow-sm">
                        <h6 class="card-subtitle mb-2">Active Teachers</h6>
                        <i class="fas fa-users fs-1 opacity-25 position-absolute top-0 end-0 mt-3 me-3"></i>
                        <h2 class="mb-3"><?php echo $activeTeachers ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card">
                    <div class="card-body border-warning border-start border-5 rounded shadow-sm">
                        <h6 class="card-subtitle mb-2">Set A (students)</h6>
                        <i class="fa fa-user-circle fs-1 opacity-25 position-absolute top-0 end-0 mt-3 me-3"></i>
                        <h2 class="mb-3"><?php echo $setA ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card">
                    <div class="card-body border-danger border-start border-5 rounded shadow-sm">
                        <h6 class="card-subtitle mb-2">Set B (students)</h6>
                        <i class="fa fa-user-circle fs-1 opacity-25 position-absolute top-0 end-0 mt-3 me-3"></i>
                        <h2 class="mb-3"><?php echo $setB ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>