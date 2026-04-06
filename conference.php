<?php
require_once 'includes.php';
page_header('Welcome to TechConf 2025', 'home');
?>

<p>Welcome to the <strong>TechConf 2025 Organizers Portal</strong>. Use the navigation above or the links
below to manage all aspects of the conference.</p>

<div class="card-grid">

    <a class="card" href="attendees.php">
        <div class="card-icon">&#128101;</div>
        <div class="card-title">Attendees</div>
        <div class="card-desc">View all attendees by category, or add a new attendee</div>
    </a>

    <a class="card" href="schedule.php">
        <div class="card-icon">&#128197;</div>
        <div class="card-title">Schedule</div>
        <div class="card-desc">Browse the conference schedule by day</div>
    </a>

    <a class="card" href="committee.php">
        <div class="card-icon">&#128101;</div>
        <div class="card-title">Committees</div>
        <div class="card-desc">View members of each organizing sub-committee</div>
    </a>

    <a class="card" href="hotel_room.php">
        <div class="card-icon">&#127968;</div>
        <div class="card-title">Hotel Rooms</div>
        <div class="card-desc">See which students are assigned to each room</div>
    </a>

    <a class="card" href="sponsors.php">
        <div class="card-icon">&#127942;</div>
        <div class="card-title">Sponsors</div>
        <div class="card-desc">View sponsoring companies and their levels</div>
    </a>

    <a class="card" href="jobs.php">
        <div class="card-icon">&#128188;</div>
        <div class="card-title">Job Board</div>
        <div class="card-desc">Browse all job postings or filter by company</div>
    </a>

    <a class="card" href="financials.php">
        <div class="card-icon">&#128176;</div>
        <div class="card-title">Financials</div>
        <div class="card-desc">Total registration income and sponsorship intake</div>
    </a>

    <a class="card" href="add_attendee.php">
        <div class="card-icon">&#10133;</div>
        <div class="card-title">Add Attendee</div>
        <div class="card-desc">Register a new student, professional, or sponsor</div>
    </a>

    <a class="card" href="add_company.php">
        <div class="card-icon">&#127970;</div>
        <div class="card-title">Add Sponsor Company</div>
        <div class="card-desc">Register a new sponsoring company</div>
    </a>

    <a class="card" href="delete_company.php">
        <div class="card-icon">&#128465;</div>
        <div class="card-title">Remove Sponsor</div>
        <div class="card-desc">Delete a sponsoring company and its attendees</div>
    </a>

    <a class="card" href="switch_session.php">
        <div class="card-icon">&#128260;</div>
        <div class="card-title">Edit Session</div>
        <div class="card-desc">Change a session's day, time, or room location</div>
    </a>

</div>

<?php page_footer(); ?>
