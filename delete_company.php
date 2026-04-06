<?php
require_once 'config.php';
require_once 'includes.php';
page_header('Remove Sponsoring Company', 'sponsors');

$message = '';
$error   = '';

// Fetch all companies for the dropdown
$companies = $pdo->query(
    "SELECT sc.companyID, sc.companyName, sc.sponsorLevel,
            COUNT(sa.attendeeID) AS numReps
     FROM SponsoringCompany sc
     LEFT JOIN SponsorAttendee sa ON sa.companyID = sc.companyID
     GROUP BY sc.companyID
     ORDER BY sc.companyName"
)->fetchAll(PDO::FETCH_ASSOC);

$selectedID   = (int)($_POST['companyID']   ?? $_GET['companyID'] ?? 0);
$confirmed    = isset($_POST['confirmed']) && $_POST['confirmed'] === '1';
$selectedInfo = null;

// Look up the selected company info for the confirmation step
if ($selectedID > 0) {
    foreach ($companies as $c) {
        if ((int)$c['companyID'] === $selectedID) {
            $selectedInfo = $c;
            break;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $confirmed && $selectedID > 0 && $selectedInfo) {
    // Step 1: collect attendeeIDs for this company's sponsor reps
    $stmt = $pdo->prepare("SELECT attendeeID FROM SponsorAttendee WHERE companyID = ?");
    $stmt->execute([$selectedID]);
    $attendeeIDs = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Step 2: delete the attendees (cascades SponsorAttendee and Speaks rows)
    if (!empty($attendeeIDs)) {
        $placeholders = implode(',', array_fill(0, count($attendeeIDs), '?'));
        $pdo->prepare("DELETE FROM Attendee WHERE attendeeID IN ($placeholders)")
            ->execute($attendeeIDs);
    }

    // Step 3: delete the company (cascades remaining SponsorAttendee and JobAd rows)
    $pdo->prepare("DELETE FROM SponsoringCompany WHERE companyID = ?")->execute([$selectedID]);

    $numDeleted = count($attendeeIDs);
    $message = "Company <strong>" . htmlspecialchars($selectedInfo['companyName']) . "</strong> and "
             . "<strong>$numDeleted</strong> associated attendee(s) have been removed.";

    // Refresh company list after deletion
    $companies = $pdo->query(
        "SELECT sc.companyID, sc.companyName, sc.sponsorLevel,
                COUNT(sa.attendeeID) AS numReps
         FROM SponsoringCompany sc
         LEFT JOIN SponsorAttendee sa ON sa.companyID = sc.companyID
         GROUP BY sc.companyID
         ORDER BY sc.companyName"
    )->fetchAll(PDO::FETCH_ASSOC);
    $selectedID   = 0;
    $selectedInfo = null;
}
?>

<?php if ($message): ?>
    <p class="alert alert-success"><?= $message ?></p>
    <p><a class="btn btn-secondary" href="sponsors.php">View Sponsors</a></p>
<?php endif; ?>

<?php if ($error): ?>
    <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if (!$message): ?>

<?php if (empty($companies)): ?>
    <p class="alert alert-info">There are no sponsoring companies to remove.</p>
<?php else: ?>

    <!-- Step 1: choose company -->
    <form method="post" action="delete_company.php">
        <div class="form-group" style="max-width:420px;">
            <label for="companyID">Select Company to Remove</label>
            <select id="companyID" name="companyID" required>
                <option value="">-- Choose a company --</option>
                <?php foreach ($companies as $c): ?>
                    <option value="<?= $c['companyID'] ?>"
                        <?= $selectedID === (int)$c['companyID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['companyName']) ?>
                        (<?= htmlspecialchars($c['sponsorLevel']) ?>,
                        <?= (int)$c['numReps'] ?> rep<?= $c['numReps'] != 1 ? 's' : '' ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="btn btn-danger" type="submit" name="confirmed" value="0">
            Review Deletion
        </button>
        <a class="btn btn-secondary" href="sponsors.php" style="margin-left:8px;">Cancel</a>
    </form>

    <!-- Step 2: confirmation -->
    <?php if ($selectedID > 0 && $selectedInfo && !$confirmed): ?>
        <div class="alert alert-error" style="margin-top:24px;max-width:520px;">
            <strong>Warning:</strong> You are about to permanently delete
            <strong><?= htmlspecialchars($selectedInfo['companyName']) ?></strong>
            (<?= htmlspecialchars($selectedInfo['sponsorLevel']) ?> sponsor)
            along with <strong><?= (int)$selectedInfo['numReps'] ?></strong>
            associated attendee(s) and all their job postings.<br><br>
            This action <strong>cannot be undone</strong>.
        </div>
        <form method="post" action="delete_company.php">
            <input type="hidden" name="companyID" value="<?= $selectedID ?>">
            <input type="hidden" name="confirmed" value="1">
            <button class="btn btn-danger" type="submit">Yes, Delete Permanently</button>
            <a class="btn btn-secondary" href="delete_company.php" style="margin-left:8px;">Cancel</a>
        </form>
    <?php endif; ?>

<?php endif; ?>
<?php endif; ?>

<?php page_footer(); ?>
