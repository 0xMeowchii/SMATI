<?php
include '../database.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'includes/header.php'; ?>
</head>

<body>
    <!-- Sidebar -->
    <?php
    include('includes/sidebar.php');

    //Fetch Announcements
    $conn = connectToDB();
    $sql = "SELECT * FROM announcements ORDER BY announcement_id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $announcements = [];
    while ($row = $result->fetch_assoc()) {
        $announcements[] = [
            'title' => $row['title'],
            'details' => $row['details'],
            'priority' => $row['type'],
            'date' => new DateTime($row['createdAt'])
        ];
    }

    ?>


    <main class="main-content">
        <div class="card border-0 text-white mb-4" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6);">
            <div class="card-body py-4">
                <div class="row align-items-center">
                    <div class="col-12">
                        <h1 class="card-title h2 mb-2 fw-bold">Welcome back, Registrar!</h1>
                        <p class="card-text mb-1 opacity-75 fw-semibold">Education is the most powerful weapon which you can use to change the world.” — Nelson Mandela</p>
                        <p class="card-text mb-0 opacity-50 fst-italic">Good morning! Believe in your goals today — every class, every effort counts toward your dream.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border rounded-3 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold fs-5">Announcements</span>
                        <span class="badge bg-warning text-dark" data-badge-type="announcements-count"></span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="input-group">
                                <input type="text" class="form-control border-end-0" id="searchAnnouncements" placeholder="Search announcements...">
                                <span class="input-group-text bg-white border-start-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-wrap gap-2" id="announcementsFilterButtons">
                                <button class="btn btn-sm btn-primary announcements-filter-btn active" data-announcement-filter="all">All</button>
                                <button class="btn btn-sm btn-outline-primary announcements-filter-btn" data-announcement-filter="low">Low</button>
                                <button class="btn btn-sm btn-outline-primary announcements-filter-btn" data-announcement-filter="high">High</button>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1 overflow-auto" id="announcementsListContainer" style="max-height:450px;">
                        <?php foreach ($announcements as $announcement): ?>
                            <?php
                            // Determine badge class and icon based on status
                            $badge_class = '';

                            switch ($announcement['priority']) {
                                case 'Low':
                                    $badge_class = 'bg-warning text-black';
                                    break;
                                case 'High':
                                    $badge_class = 'bg-danger text-white';
                                    break;
                            }
                            ?>
                            <div class="card mb-3 rounded-4 card-announcements"
                                data-announcement-title="<?php echo strtolower($announcement['title']); ?>"
                                data-announcement-priority="<?php echo strtolower($announcement['priority']); ?>">
                                <div class="card-body border-start border-5 rounded-4 border-primary">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title mb-0 fw-bold announcement-title"><?php echo $announcement['title'] ?></h5>
                                        <div class="d-flex gap-1">
                                            <span class="badge <?php echo $badge_class; ?>"><?php echo $announcement['priority'] ?></span>
                                        </div>
                                    </div>
                                    <p class="card-text text-muted mb-3"><?php echo $announcement['details'] ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>Admin •
                                            <i class="fas fa-clock me-1 ms-2"></i><?php echo $announcement['date']->format('m-d-Y') ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

    </main>


    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        (function() {
            'use strict';

            // Cache DOM elements
            const searchInput = document.getElementById('searchAnnouncements');
            const filterButtons = document.querySelectorAll('.announcements-filter-btn');
            const announcementsCard = document.querySelectorAll('.card-announcements');
            const announcementsContainer = document.getElementById('announcementsListContainer');
            const announcementsBadge = document.querySelector('[data-badge-type="announcements-count"]');

            // Current filter state
            let currentFilter = 'all';
            let currentSearch = '';

            // Initialize the system
            function init() {
                if (!searchInput || !filterButtons.length || !announcementsCard.length) {
                    console.warn('Announcements filtering elements not found');
                    return;
                }

                // Add event listeners
                searchInput.addEventListener('input', handleSearch);
                filterButtons.forEach(btn => {
                    btn.addEventListener('click', handleFilterClick);
                });

                // Initial display
                applyFilters();
            }

            // Handle search input
            function handleSearch(e) {
                currentSearch = e.target.value.toLowerCase().trim();
                applyFilters();
            }

            // Handle filter button click
            function handleFilterClick(e) {
                const btn = e.currentTarget;
                const filter = btn.getAttribute('data-announcement-filter');

                // Update active state
                filterButtons.forEach(b => {
                    b.classList.remove('active', 'btn-primary');
                    b.classList.add('btn-outline-primary');
                });
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('active', 'btn-primary');

                // Update current filter
                currentFilter = filter;
                applyFilters();
            }

            // Apply both search and filter
            function applyFilters() {
                let visibleCount = 0;

                announcementsCard.forEach(card => {
                    const priority = card.getAttribute('data-announcement-priority');
                    const title = card.getAttribute('data-announcement-title');

                    // Check filter match
                    const filterMatch = currentFilter === 'all' || priority === currentFilter;

                    // Check search match
                    const searchMatch = currentSearch === '' || title.includes(currentSearch);

                    // Show or hide card
                    if (filterMatch && searchMatch) {
                        card.style.display = '';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }

                });

                if (announcementsBadge) {
                    announcementsBadge.textContent = `${visibleCount} Announcement/s`;
                }

                // Show "no results" message if needed
                showNoResultsMessage(visibleCount);
            }

            // Show or hide "no results" message
            function showNoResultsMessage(count) {
                // Remove existing message
                const existingMsg = document.getElementById('announcementNoResultsMsg');
                if (existingMsg) {
                    existingMsg.remove();
                }

                // Add message if no results
                if (count === 0) {
                    const noResultsDiv = document.createElement('div');
                    noResultsDiv.id = 'announcementNoResultsMsg';
                    noResultsDiv.className = 'text-center py-5';
                    noResultsDiv.innerHTML = `
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No announcements found</h5>
                <p class="text-muted">Try adjusting your search or filter criteria</p>
            `;
                    announcementsContainer.appendChild(noResultsDiv);
                }
            }

            // Initialize when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
    </script>
</body>

</html>