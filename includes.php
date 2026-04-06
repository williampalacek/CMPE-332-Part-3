<?php
// Shared header and footer functions

function page_header(string $title, string $active = ''): void {
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($title) . ' &mdash; TechConf 2025</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <img src="conference_logo.svg" alt="TechConf 2025 Logo">
    <div class="header-text">
        <h1>TechConf 2025</h1>
        <p>Conference Management System &mdash; Organizers Portal</p>
    </div>
</header>

<nav>
    <a href="conference.php"' . ($active === 'home'        ? ' class="active"' : '') . '>Home</a>
    <a href="attendees.php"' . ($active === 'attendees'    ? ' class="active"' : '') . '>Attendees</a>
    <a href="schedule.php"'  . ($active === 'schedule'     ? ' class="active"' : '') . '>Schedule</a>
    <a href="committee.php"' . ($active === 'committee'    ? ' class="active"' : '') . '>Committees</a>
    <a href="hotel_room.php"'. ($active === 'hotel'        ? ' class="active"' : '') . '>Hotel Rooms</a>
    <a href="sponsors.php"'  . ($active === 'sponsors'     ? ' class="active"' : '') . '>Sponsors</a>
    <a href="jobs.php"'      . ($active === 'jobs'         ? ' class="active"' : '') . '>Jobs</a>
    <a href="financials.php"'. ($active === 'financials'   ? ' class="active"' : '') . '>Financials</a>
</nav>

<main>
    <h2>' . htmlspecialchars($title) . '</h2>
';
}

function page_footer(): void {
    echo '
</main>
<footer>
    <p>TechConf 2025 Conference Management System &mdash; For Internal Use Only</p>
</footer>
</body>
</html>';
}
?>
