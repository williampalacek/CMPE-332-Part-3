<?php
require_once 'config.php';
require_once 'includes.php';
page_header('Conference Financials', 'financials');

// Registration totals: students $50, professionals $100, sponsors free
$regStmt = $pdo->query(
    "SELECT
        SUM(CASE attendeeType WHEN 'student'      THEN 50
                              WHEN 'professional' THEN 100
                              ELSE 0 END)               AS totalRegistration,
        SUM(CASE WHEN attendeeType = 'student'      THEN 1 ELSE 0 END) AS numStudents,
        SUM(CASE WHEN attendeeType = 'professional' THEN 1 ELSE 0 END) AS numProfessionals,
        SUM(CASE WHEN attendeeType = 'sponsor'      THEN 1 ELSE 0 END) AS numSponsors,
        COUNT(*)                                         AS totalAttendees
     FROM Attendee"
);
$reg = $regStmt->fetch(PDO::FETCH_ASSOC);

// Sponsorship totals per company
$sponsorStmt = $pdo->query(
    "SELECT companyName, sponsorLevel,
            CASE sponsorLevel
                WHEN 'Platinum' THEN 10000
                WHEN 'Gold'     THEN 5000
                WHEN 'Silver'   THEN 3000
                WHEN 'Bronze'   THEN 1000
                ELSE 0 END AS contribution
     FROM SponsoringCompany
     ORDER BY FIELD(sponsorLevel,'Platinum','Gold','Silver','Bronze'), companyName"
);
$sponsorRows = $sponsorStmt->fetchAll(PDO::FETCH_ASSOC);

$totalSponsorship = array_sum(array_column($sponsorRows, 'contribution'));
$grandTotal       = (float)$reg['totalRegistration'] + $totalSponsorship;

$levelBadge = [
    'Platinum' => 'badge-platinum',
    'Gold'     => 'badge-gold',
    'Silver'   => 'badge-silver',
    'Bronze'   => 'badge-bronze',
];
?>

<!-- Summary cards -->
<div style="display:flex;gap:18px;flex-wrap:wrap;margin-bottom:28px;">
    <div style="background:#fff;border:1px solid #d0dce9;border-radius:8px;padding:20px 28px;min-width:180px;text-align:center;box-shadow:0 1px 4px rgba(0,0,0,.07);">
        <div style="font-size:1.6rem;font-weight:700;color:#2c5f8a;">$<?= number_format($reg['totalRegistration']) ?></div>
        <div style="font-size:.85rem;color:#555;margin-top:4px;">Total Registration Fees</div>
    </div>
    <div style="background:#fff;border:1px solid #d0dce9;border-radius:8px;padding:20px 28px;min-width:180px;text-align:center;box-shadow:0 1px 4px rgba(0,0,0,.07);">
        <div style="font-size:1.6rem;font-weight:700;color:#2c5f8a;">$<?= number_format($totalSponsorship) ?></div>
        <div style="font-size:.85rem;color:#555;margin-top:4px;">Total Sponsorship Funds</div>
    </div>
    <div style="background:#1a3a5c;border:1px solid #14304e;border-radius:8px;padding:20px 28px;min-width:180px;text-align:center;box-shadow:0 1px 4px rgba(0,0,0,.07);">
        <div style="font-size:1.6rem;font-weight:700;color:#fff;">$<?= number_format($grandTotal) ?></div>
        <div style="font-size:.85rem;color:#c8dff5;margin-top:4px;">Grand Total Intake</div>
    </div>
</div>

<!-- Registration breakdown -->
<h3>Registration Breakdown</h3>
<div class="table-wrap">
<table>
    <thead>
        <tr><th>Category</th><th>Count</th><th>Fee per Person</th><th>Subtotal</th></tr>
    </thead>
    <tbody>
        <tr>
            <td>Students</td>
            <td><?= (int)$reg['numStudents'] ?></td>
            <td>$50</td>
            <td>$<?= number_format((int)$reg['numStudents'] * 50) ?></td>
        </tr>
        <tr>
            <td>Professionals</td>
            <td><?= (int)$reg['numProfessionals'] ?></td>
            <td>$100</td>
            <td>$<?= number_format((int)$reg['numProfessionals'] * 100) ?></td>
        </tr>
        <tr>
            <td>Sponsor Representatives</td>
            <td><?= (int)$reg['numSponsors'] ?></td>
            <td>Free</td>
            <td>$0</td>
        </tr>
        <tr style="font-weight:bold;background:#e8f0f8;">
            <td>Total</td>
            <td><?= (int)$reg['totalAttendees'] ?></td>
            <td>&mdash;</td>
            <td>$<?= number_format($reg['totalRegistration']) ?></td>
        </tr>
    </tbody>
</table>
</div>

<!-- Sponsorship breakdown -->
<h3>Sponsorship Breakdown</h3>
<?php if (empty($sponsorRows)): ?>
    <p class="alert alert-info">No sponsoring companies found.</p>
<?php else: ?>
    <div class="table-wrap">
    <table>
        <thead>
            <tr><th>Company</th><th>Level</th><th>Contribution</th></tr>
        </thead>
        <tbody>
            <?php foreach ($sponsorRows as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['companyName']) ?></td>
                <td><span class="badge <?= $levelBadge[$s['sponsorLevel']] ?? '' ?>"><?= htmlspecialchars($s['sponsorLevel']) ?></span></td>
                <td>$<?= number_format($s['contribution']) ?></td>
            </tr>
            <?php endforeach; ?>
            <tr style="font-weight:bold;background:#e8f0f8;">
                <td colspan="2">Total</td>
                <td>$<?= number_format($totalSponsorship) ?></td>
            </tr>
        </tbody>
    </table>
    </div>
<?php endif; ?>

<?php page_footer(); ?>
