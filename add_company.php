<?php
require_once 'config.php';
require_once 'includes.php';
page_header('Add Sponsoring Company', 'sponsors');

$message = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $companyName  = trim($_POST['companyName']  ?? '');
    $sponsorLevel = $_POST['sponsorLevel']      ?? '';
    $emailsSent   = (int)($_POST['emailsSent']  ?? 0);

    $validLevels = ['Platinum', 'Gold', 'Silver', 'Bronze'];

    if ($companyName === '' || !in_array($sponsorLevel, $validLevels)) {
        $error = 'Please provide a company name and select a valid sponsorship level.';
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO SponsoringCompany (companyName, sponsorLevel, emailsSent) VALUES (?, ?, ?)"
        );
        $stmt->execute([$companyName, $sponsorLevel, max(0, $emailsSent)]);
        $message = "Company <strong>" . htmlspecialchars($companyName) . "</strong> added as a "
                 . htmlspecialchars($sponsorLevel) . " sponsor.";
    }
}
?>

<?php if ($message): ?>
    <p class="alert alert-success"><?= $message ?></p>
    <p>
        <a class="btn btn-secondary" href="sponsors.php">View All Sponsors</a>
        &nbsp;<a class="btn btn-primary" href="add_company.php">Add Another</a>
    </p>
<?php endif; ?>

<?php if ($error): ?>
    <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if (!$message): ?>
<div class="form-box">
    <form method="post" action="add_company.php">

        <div class="form-group">
            <label for="companyName">Company Name <span style="color:#c0392b">*</span></label>
            <input type="text" id="companyName" name="companyName"
                   value="<?= htmlspecialchars($_POST['companyName'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="sponsorLevel">Sponsorship Level <span style="color:#c0392b">*</span></label>
            <select id="sponsorLevel" name="sponsorLevel" required>
                <option value="">-- Select level --</option>
                <?php
                $levels = [
                    'Platinum' => 'Platinum ($10,000)',
                    'Gold'     => 'Gold ($5,000)',
                    'Silver'   => 'Silver ($3,000)',
                    'Bronze'   => 'Bronze ($1,000)',
                ];
                foreach ($levels as $val => $label):
                ?>
                    <option value="<?= $val ?>"
                        <?= (($_POST['sponsorLevel'] ?? '') === $val) ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="emailsSent">Emails Already Sent</label>
            <input type="number" id="emailsSent" name="emailsSent" min="0" max="10"
                   value="<?= (int)($_POST['emailsSent'] ?? 0) ?>">
            <span class="hint">Platinum: 5 max &bull; Gold: 4 &bull; Silver: 3 &bull; Bronze: 0</span>
        </div>

        <button class="btn btn-primary" type="submit">Add Company</button>
        <a class="btn btn-secondary" href="sponsors.php" style="margin-left:8px;">Cancel</a>

    </form>
</div>
<?php endif; ?>

<?php page_footer(); ?>
