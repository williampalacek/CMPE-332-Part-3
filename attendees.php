<?php
require_once 'config.php';
require_once 'includes.php';
page_header('Conference Attendees', 'attendees');

// Students
$students = $pdo->query(
    "SELECT a.firstName, a.lastName, a.email, s.roomNumber
     FROM Attendee a
     JOIN Student s ON s.attendeeID = a.attendeeID
     ORDER BY a.lastName, a.firstName"
)->fetchAll(PDO::FETCH_ASSOC);

// Professionals
$professionals = $pdo->query(
    "SELECT a.firstName, a.lastName, a.email, p.organization
     FROM Attendee a
     JOIN Professional p ON p.attendeeID = a.attendeeID
     ORDER BY a.lastName, a.firstName"
)->fetchAll(PDO::FETCH_ASSOC);

// Sponsors
$sponsors = $pdo->query(
    "SELECT a.firstName, a.lastName, a.email, sc.companyName, sc.sponsorLevel
     FROM Attendee a
     JOIN SponsorAttendee sa ON sa.attendeeID = a.attendeeID
     JOIN SponsoringCompany sc ON sc.companyID = sa.companyID
     ORDER BY sc.companyName, a.lastName, a.firstName"
)->fetchAll(PDO::FETCH_ASSOC);

$levelBadge = [
    'Platinum' => 'badge-platinum',
    'Gold'     => 'badge-gold',
    'Silver'   => 'badge-silver',
    'Bronze'   => 'badge-bronze',
];
?>

<p>
    <a class="btn btn-primary" href="add_attendee.php">+ Add New Attendee</a>
</p>

<!-- ---- Students ---- -->
<h3>Students (<?= count($students) ?>)</h3>
<?php if (empty($students)): ?>
    <p class="alert alert-info">No students registered.</p>
<?php else: ?>
    <div class="table-wrap">
    <table>
        <thead>
            <tr><th>First Name</th><th>Last Name</th><th>Email</th><th>Hotel Room</th></tr>
        </thead>
        <tbody>
            <?php foreach ($students as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['firstName']) ?></td>
                <td><?= htmlspecialchars($s['lastName']) ?></td>
                <td><?= htmlspecialchars($s['email']) ?></td>
                <td><?= $s['roomNumber'] ? htmlspecialchars($s['roomNumber']) : '<em>Not assigned</em>' ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
<?php endif; ?>

<!-- ---- Professionals ---- -->
<h3>Professionals (<?= count($professionals) ?>)</h3>
<?php if (empty($professionals)): ?>
    <p class="alert alert-info">No professionals registered.</p>
<?php else: ?>
    <div class="table-wrap">
    <table>
        <thead>
            <tr><th>First Name</th><th>Last Name</th><th>Email</th><th>Organization</th></tr>
        </thead>
        <tbody>
            <?php foreach ($professionals as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['firstName']) ?></td>
                <td><?= htmlspecialchars($p['lastName']) ?></td>
                <td><?= htmlspecialchars($p['email']) ?></td>
                <td><?= htmlspecialchars($p['organization'] ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
<?php endif; ?>

<!-- ---- Sponsors ---- -->
<h3>Sponsor Representatives (<?= count($sponsors) ?>)</h3>
<?php if (empty($sponsors)): ?>
    <p class="alert alert-info">No sponsor attendees registered.</p>
<?php else: ?>
    <div class="table-wrap">
    <table>
        <thead>
            <tr><th>First Name</th><th>Last Name</th><th>Email</th><th>Company</th><th>Level</th></tr>
        </thead>
        <tbody>
            <?php foreach ($sponsors as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['firstName']) ?></td>
                <td><?= htmlspecialchars($s['lastName']) ?></td>
                <td><?= htmlspecialchars($s['email']) ?></td>
                <td><?= htmlspecialchars($s['companyName']) ?></td>
                <td>
                    <span class="badge <?= $levelBadge[$s['sponsorLevel']] ?? '' ?>">
                        <?= htmlspecialchars($s['sponsorLevel']) ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
<?php endif; ?>

<?php page_footer(); ?>
