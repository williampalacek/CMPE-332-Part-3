<?php
require_once 'config.php';
require_once 'includes.php';
page_header('Add New Attendee', 'attendees');

// Fetch hotel rooms and companies for dropdowns
$rooms = $pdo->query("SELECT roomNumber, numBeds FROM HotelRoom ORDER BY roomNumber")
             ->fetchAll(PDO::FETCH_ASSOC);
$companies = $pdo->query("SELECT companyID, companyName FROM SponsoringCompany ORDER BY companyName")
                 ->fetchAll(PDO::FETCH_ASSOC);

$message = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName  = trim($_POST['lastName']  ?? '');
    $email     = trim($_POST['email']     ?? '');
    $type      = $_POST['attendeeType']   ?? '';

    $validTypes = ['student', 'professional', 'sponsor'];

    if ($firstName === '' || $lastName === '' || !in_array($type, $validTypes)) {
        $error = 'Please fill in all required fields and select a valid attendee type.';
    } else {
        // Insert into Attendee table
        $stmt = $pdo->prepare(
            "INSERT INTO Attendee (firstName, lastName, email, attendeeType) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$firstName, $lastName, $email ?: null, $type]);
        $attendeeID = (int)$pdo->lastInsertId();

        if ($type === 'student') {
            $roomNumber = $_POST['roomNumber'] ?? '';
            $roomNumber = $roomNumber !== '' ? $roomNumber : null;

            $stmt = $pdo->prepare("INSERT INTO Student (attendeeID, roomNumber) VALUES (?, ?)");
            $stmt->execute([$attendeeID, $roomNumber]);

            $message = "Student <strong>" . htmlspecialchars($firstName . ' ' . $lastName)
                     . "</strong> added successfully.";
            if ($roomNumber) {
                $message .= " Assigned to room <strong>" . htmlspecialchars($roomNumber) . "</strong>.";
            } else {
                $message .= " No hotel room assigned.";
            }

        } elseif ($type === 'professional') {
            $organization = trim($_POST['organization'] ?? '');
            $stmt = $pdo->prepare("INSERT INTO Professional (attendeeID, organization) VALUES (?, ?)");
            $stmt->execute([$attendeeID, $organization ?: null]);

            $message = "Professional <strong>" . htmlspecialchars($firstName . ' ' . $lastName)
                     . "</strong> added successfully.";

        } elseif ($type === 'sponsor') {
            $companyID = (int)($_POST['companyID'] ?? 0);
            if ($companyID <= 0) {
                // Roll back the Attendee insert by deleting it
                $pdo->prepare("DELETE FROM Attendee WHERE attendeeID = ?")->execute([$attendeeID]);
                $error = 'Please select a company for the sponsor attendee.';
            } else {
                $stmt = $pdo->prepare("INSERT INTO SponsorAttendee (attendeeID, companyID) VALUES (?, ?)");
                $stmt->execute([$attendeeID, $companyID]);

                $message = "Sponsor attendee <strong>" . htmlspecialchars($firstName . ' ' . $lastName)
                         . "</strong> added successfully.";
            }
        }
    }
}
?>

<?php if ($message): ?>
    <p class="alert alert-success"><?= $message ?></p>
    <p><a class="btn btn-secondary" href="attendees.php">View All Attendees</a>
       &nbsp;<a class="btn btn-primary" href="add_attendee.php">Add Another</a></p>
<?php endif; ?>

<?php if ($error): ?>
    <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if (!$message): ?>
<div class="form-box">
    <form method="post" action="add_attendee.php">

        <div class="form-group">
            <label for="firstName">First Name <span style="color:#c0392b">*</span></label>
            <input type="text" id="firstName" name="firstName"
                   value="<?= htmlspecialchars($_POST['firstName'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="lastName">Last Name <span style="color:#c0392b">*</span></label>
            <input type="text" id="lastName" name="lastName"
                   value="<?= htmlspecialchars($_POST['lastName'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="attendeeType">Attendee Type <span style="color:#c0392b">*</span></label>
            <select id="attendeeType" name="attendeeType" required>
                <option value="">-- Select type --</option>
                <option value="student"      <?= (($_POST['attendeeType'] ?? '') === 'student')      ? 'selected' : '' ?>>Student ($50)</option>
                <option value="professional" <?= (($_POST['attendeeType'] ?? '') === 'professional') ? 'selected' : '' ?>>Professional ($100)</option>
                <option value="sponsor"      <?= (($_POST['attendeeType'] ?? '') === 'sponsor')      ? 'selected' : '' ?>>Sponsor (Free)</option>
            </select>
        </div>

        <!-- Student only -->
        <div class="form-group">
            <label for="roomNumber">Hotel Room <span style="font-weight:normal;color:#777;">(Students only &mdash; optional)</span></label>
            <select id="roomNumber" name="roomNumber">
                <option value="">-- No room --</option>
                <?php foreach ($rooms as $r): ?>
                    <option value="<?= htmlspecialchars($r['roomNumber']) ?>"
                        <?= (($_POST['roomNumber'] ?? '') === $r['roomNumber']) ? 'selected' : '' ?>>
                        Room <?= htmlspecialchars($r['roomNumber']) ?>
                        (<?= (int)$r['numBeds'] ?> bed<?= $r['numBeds'] > 1 ? 's' : '' ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Professional only -->
        <div class="form-group">
            <label for="organization">Organization <span style="font-weight:normal;color:#777;">(Professionals only)</span></label>
            <input type="text" id="organization" name="organization"
                   value="<?= htmlspecialchars($_POST['organization'] ?? '') ?>">
        </div>

        <!-- Sponsor only -->
        <div class="form-group">
            <label for="companyID">Company <span style="font-weight:normal;color:#777;">(Sponsors only)</span></label>
            <select id="companyID" name="companyID">
                <option value="">-- Select company --</option>
                <?php foreach ($companies as $c): ?>
                    <option value="<?= $c['companyID'] ?>"
                        <?= (($_POST['companyID'] ?? '') == $c['companyID']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['companyName']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button class="btn btn-primary" type="submit">Add Attendee</button>
        <a class="btn btn-secondary" href="attendees.php" style="margin-left:8px;">Cancel</a>

    </form>
</div>
<?php endif; ?>

<?php page_footer(); ?>
