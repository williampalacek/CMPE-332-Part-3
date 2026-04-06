<?php
require_once 'config.php';
require_once 'includes.php';
page_header('Sponsors', 'sponsors');

// Fetch all sponsoring companies ordered by level then name
$sponsors = $pdo->query(
    "SELECT companyID, companyName, sponsorLevel, emailsSent
     FROM SponsoringCompany
     ORDER BY FIELD(sponsorLevel,'Platinum','Gold','Silver','Bronze'), companyName"
)->fetchAll(PDO::FETCH_ASSOC);

$levelBadge = [
    'Platinum' => 'badge-platinum',
    'Gold'     => 'badge-gold',
    'Silver'   => 'badge-silver',
    'Bronze'   => 'badge-bronze',
];
?>

<h3>Sponsoring Companies</h3>

<?php if (empty($sponsors)): ?>
    <p class="alert alert-info">No sponsoring companies found.</p>
<?php else: ?>
    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Company Name</th>
                <th>Sponsorship Level</th>
                <th>Contribution</th>
                <th>Emails Sent</th>
                <th>Email Limit</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $amounts = ['Platinum' => 10000, 'Gold' => 5000, 'Silver' => 3000, 'Bronze' => 1000];
            $limits  = ['Platinum' => 5,     'Gold' => 4,    'Silver' => 3,    'Bronze' => 0];
            foreach ($sponsors as $s):
                $level = $s['sponsorLevel'];
            ?>
            <tr>
                <td><?= htmlspecialchars($s['companyName']) ?></td>
                <td>
                    <span class="badge <?= $levelBadge[$level] ?? '' ?>">
                        <?= htmlspecialchars($level) ?>
                    </span>
                </td>
                <td>$<?= number_format($amounts[$level]) ?></td>
                <td><?= (int)$s['emailsSent'] ?></td>
                <td><?= $limits[$level] > 0 ? $limits[$level] : 'None' ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
<?php endif; ?>

<p>
    <a class="btn btn-primary" href="add_company.php">Add New Sponsor Company</a>
    &nbsp;
    <a class="btn btn-danger" href="delete_company.php">Remove Sponsor Company</a>
</p>

<?php page_footer(); ?>
