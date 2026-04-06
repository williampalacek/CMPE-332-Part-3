<?php
require_once 'config.php';
require_once 'includes.php';
page_header('Job Board', 'jobs');

// Fetch companies for filter dropdown
$companies = $pdo->query("SELECT companyID, companyName FROM SponsoringCompany ORDER BY companyName")
                 ->fetchAll(PDO::FETCH_ASSOC);

$selectedCompany = isset($_GET['companyID']) ? (int)$_GET['companyID'] : 0;

// Build query — filter by company if one is selected
if ($selectedCompany > 0) {
    $stmt = $pdo->prepare(
        "SELECT j.title, j.city, j.province, j.payRate, sc.companyName
         FROM JobAd j
         JOIN SponsoringCompany sc ON sc.companyID = j.companyID
         WHERE j.companyID = ?
         ORDER BY j.title"
    );
    $stmt->execute([$selectedCompany]);
} else {
    $stmt = $pdo->query(
        "SELECT j.title, j.city, j.province, j.payRate, sc.companyName
         FROM JobAd j
         JOIN SponsoringCompany sc ON sc.companyID = j.companyID
         ORDER BY sc.companyName, j.title"
    );
}
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<form method="get" action="jobs.php">
    <div class="form-group" style="max-width:380px;">
        <label for="companyID">Filter by Company <em style="font-weight:normal;">(optional)</em></label>
        <select name="companyID" id="companyID">
            <option value="0">-- All Companies --</option>
            <?php foreach ($companies as $c): ?>
                <option value="<?= $c['companyID'] ?>"
                    <?= $selectedCompany === (int)$c['companyID'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['companyName']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <button class="btn btn-primary" type="submit">Search</button>
    <?php if ($selectedCompany > 0): ?>
        <a class="btn btn-secondary" href="jobs.php" style="margin-left:8px;">Clear Filter</a>
    <?php endif; ?>
</form>

<h3>
    <?php if ($selectedCompany > 0):
        foreach ($companies as $c) {
            if ((int)$c['companyID'] === $selectedCompany) {
                echo 'Jobs at ' . htmlspecialchars($c['companyName']);
                break;
            }
        }
    else: ?>
        All Available Jobs
    <?php endif; ?>
</h3>

<?php if (empty($jobs)): ?>
    <p class="alert alert-info">No job postings found.</p>
<?php else: ?>
    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Job Title</th>
                <th>Company</th>
                <th>City</th>
                <th>Province</th>
                <th>Annual Pay</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($jobs as $j): ?>
            <tr>
                <td><?= htmlspecialchars($j['title']) ?></td>
                <td><?= htmlspecialchars($j['companyName']) ?></td>
                <td><?= htmlspecialchars($j['city']) ?></td>
                <td><?= htmlspecialchars($j['province']) ?></td>
                <td>$<?= number_format($j['payRate']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
<?php endif; ?>

<?php page_footer(); ?>
