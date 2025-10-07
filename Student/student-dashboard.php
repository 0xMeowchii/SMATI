<?php
include('../database.php');

$conn = connectToDB();
$sql = "SELECT * FROM announcements ORDER BY announcement_id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$announcements = [];
while ($row = $result->fetch_assoc()) {
    $announcements[] = [
        'title' => $row['title'],
        'details' => $row['details']
    ];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../Student/student.css">
</head>

<body>
    <!-- Sidebar -->
    <?php include('sidebar.php'); ?>

    <main class="main-content">
        <div class="dashboard-title d-flex flex-column align-items-center justify-content-center border-bottom pb-2">
            <img class="mb-3" src="../images/smatilogo.png" alt="logo" width="80px" height="80px">
            <h4>St. Michael Arcangel Technological Institute, Inc.</h4>
        </div>

        <div class="card border rounded mt-3">
            <div class="card-header rounded-top bg-primary text-white fw-bold">
                ANNOUNCEMENTS!
            </div>
            <div class="card-body">
                Welcome to Student Portal.
            </div>
        </div>

        <?php foreach($announcements as $announcement): ?>
            <div class="card border rounded mt-3">
            <div class="card-header rounded-top bg-primary text-white fw-bold">
                <?php echo $announcement['title']?>
            </div>
            <div class="card-body">
                <?php echo $announcement['details'] ?>
            </div>
        </div>
        <?php endforeach; ?>
    </main>
</body>

</html>