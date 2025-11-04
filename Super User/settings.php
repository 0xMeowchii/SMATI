<?php
require_once 'includes/session.php';
include '../database.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'includes/header.php' ?>
    <style>
        /* Custom styles for what Bootstrap doesn't provide */
        .custom-card {
            border-radius: 12px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            overflow: hidden;
        }

        .custom-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .custom-card .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
            border-radius: 12px 12px 0 0 !important;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <?php include('includes/sidebar.php'); ?>

    <main class="main-content">
        <div class="page-header">
            <h4><i class="fas fa-cog me-2"></i>System Settings</h4>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 custom-card mb-4">
                    <div class="card-header px-4 py-3 bg-primary text-white">
                        <h5 class="card-title mb-0">Database Management</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label">Backup Data</label>
                            <p class="card-text text-muted small mb-2">Create a complete backup of all system data</p>
                            <button class="btn btn-outline-primary w-100" id="backup-btn">
                                <i class="fas fa-database me-2"></i>Create Backup
                            </button>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Restore Data</label>
                            <p class="text-muted small mb-2">Restore system from a previous backup</p>
                            <input type="file" class="form-control mb-2" id="restore-file">
                            <button class="btn btn-outline-secondary w-100" id="restore-btn">
                                <i class="fas fa-upload me-2"></i>Restore Backup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card border-0 custom-card mb-4">
                    <div class="card-header px-4 py-3 bg-success text-white">
                        <h5 class="card-title mb-0">System Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label">System Version</label>
                            <input type="text" class="form-control" value="SMATI - EduPortal v1.0.0" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Last Backup</label>
                            <input type="text" class="form-control" id="last-backup" readonly
                                title="Click for more details" style="cursor: pointer;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('backup-btn').addEventListener('click', function() {
            Swal.fire({
                title: 'Create Backup?',
                text: 'Create a backup of the database? This may take a few moments.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, create backup',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const btn = this;

                    // Store the loading SweetAlert instance
                    const loadingAlert = Swal.fire({
                        title: 'Creating Backup...',
                        text: 'Please wait while we create your database backup. This may take a few moments.',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch('backup.php', {
                            method: 'POST',
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Close the loading alert first
                            Swal.close();

                            if (data.success) {
                                Swal.fire({
                                    title: 'Backup Created!',
                                    html: `
            <div style="text-align: left;">
                <p><strong>Status:</strong> ${data.success}</p>
                <p><strong>Filename:</strong> ${data.filename || 'N/A'}</p>
                <p><strong>Size:</strong> ${data.size || ''}</p>
            </div>
        `,
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    loadLastBackup();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Backup Failed',
                                    text: data.message || 'Unknown error',
                                    icon: 'error'
                                });
                            }
                        })
                        .catch(error => {
                            // Close the loading alert first
                            Swal.close();

                            Swal.fire({
                                title: 'Request Failed',
                                text: 'Error creating backup: ' + error,
                                icon: 'error',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#d33'
                            });

                            btn.disabled = false;
                            btn.innerHTML = '<i class="fas fa-database me-2"></i>Create Backup';
                        });
                }
            });
        });

        // Restore functionality
        document.getElementById('restore-btn').addEventListener('click', function() {
            const fileInput = document.getElementById('restore-file');

            if (!fileInput.files || fileInput.files.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No File Selected',
                    text: 'Please select a backup file first',
                    confirmButtonColor: '#0d6efd'
                });
                return;
            }

            Swal.fire({
                title: 'Restore Database?',
                html: '<strong>WARNING:</strong> This will overwrite all current data with the backup file.<br><br>A safety backup will be created first.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, restore it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const btn = this;
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Restoring...';

                    const formData = new FormData();
                    formData.append('backup_file', fileInput.files[0]);

                    fetch('restore.php', {
                            method: 'POST',
                            body: formData,
                            credentials: 'same-origin'
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: data.message,
                                    confirmButtonColor: '#0d6efd'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Restore Failed',
                                    text: data.message,
                                    confirmButtonColor: '#d33'
                                });
                            }
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fas fa-upload me-2"></i>Restore Backup';
                            fileInput.value = '';
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Error restoring backup: ' + error,
                                confirmButtonColor: '#d33'
                            });
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fas fa-upload me-2"></i>Restore Backup';
                        });
                }
            });
        });

        // Load last backup info when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadLastBackup();
        });

        // Function to load last backup info
        function loadLastBackup() {
            fetch('get_last_backup.php', {
                    method: 'GET',
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('last-backup').value = data.last_backup;
                    }
                })
                .catch(error => {
                    console.error('Error loading last backup:', error);
                    document.getElementById('last-backup').value = 'Error loading info';
                });
        }

        // Show backup details on click
        document.getElementById('last-backup').addEventListener('click', function() {
            fetch('get_last_backup.php', {
                    method: 'GET',
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.details) {
                        Swal.fire({
                            title: 'Last Backup Details',
                            html: `
                    <div style="text-align: left;">
                        <p><strong>File:</strong> ${data.details.filename}</p>
                        <p><strong>Date:</strong> ${data.details.date}</p>
                        <p><strong>Time:</strong> ${data.details.time}</p>
                        <p><strong>Size:</strong> ${data.details.size}</p>
                        <p><strong>Created by:</strong> ${data.details.username}</p>
                    </div>
                `,
                            icon: 'info',
                            confirmButtonText: 'OK'
                        });
                    }
                });
        });
    </script>
</body>

</html>