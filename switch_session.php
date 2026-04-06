<?php
require_once 'config.php';
require_once 'includes.php';
page_header('Edit Session', 'schedule');

$message = '';
$error   = '';

// Fetch all sessions for the dropdown
$sessions = $pdo->query(
    "SELECT sessionID, sessionName, sessionDay, startTime, endTime, roomLocation
     FROM Session
     ORDER BY sessionDay, startTime, sessionName"
)->fetchAll(PDO::FETCH_ASSOC);

$selectedID  = (int)($_POST['sessionID'] ?? $_GET['sessionID'] ?? 0);
$sessionInfo = null;

if ($selectedID > 0) {
    foreach ($sessions as $s) {
        if ((int)$s['sessionID'] === $selectedID) {
            $sessionInfo = $s;
            break;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update']) && $selectedID > 0 && $sessionInfo) {
    $newDay      = $_POST['sessionDay']    ?? '';
    $newStart    = $_POST['startTime']     ?? '';
    $newEnd      = $_POST['endTime']       ?? '';
    $newLocation = trim($_POST['roomLocation'] ?? '');

    $validDays = ['Day 1', 'Day 2'];

    if (!in_array($newDay, $validDays) || $newStart === '' || $newEnd === '' || $newLocation === '') {
        $error = 'All fields are required. Please fill in the day, start time, end time, and location.';
    } elseif ($newStart >= $newEnd) {
        $error = 'Start time must be before end time.';
    } else {
        $stmt = $pdo->prepare(
            "UPDATE Session
             SET sessionDay = ?, startTime = ?, endTime = ?, roomLocation = ?
             WHERE sessionID = ?"
        );
        $stmt->execute([$newDay, $newStart, $newEnd, $newLocation, $selectedID]);

        $message = "Session <strong>" . htmlspecialchars($sessionInfo['sessionName'])
                 . "</strong> updated successfully.";

        // Reload session list and info after update
        $sessions = $pdo->query(
            "SELECT sessionID, sessionName, sessionDay, startTime, endTime, roomLocation
             FROM Session ORDER BY sessionDay, startTime, sessionName"
        )->fetchAll(PDO::FETCH_ASSOC);
        foreach ($sessions as $s) {
            if ((int)$s['sessionID'] === $selectedID) {
                $sessionInfo = $s;
                break;
            }
        }
    }
}
?>

<?php if ($message): ?>
    <p class="alert alert-success"><?= $message ?></p>
<?php endif; ?>
<?php if ($error): ?>
    <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<!-- Step 1: session picker -->
<form method="post" action="switch_session.php">
    <div class="form-group" style="max-width:460px;">
        <label for="sessionID">Select Session</label>
        <select id="sessionID" name="sessionID" required>
            <option value="">-- Choose a session --</option>
            <?php foreach ($sessions as $s): ?>
                <option value="<?= $s['sessionID'] ?>"
                    <?= $selectedID === (int)$s['sessionID'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s['sessionDay']) ?> &mdash;
                    <?= htmlspecialchars(date('g:i A', strtotime($s['startTime']))) ?> &mdash;
                    <?= htmlspecialchars($s['sessionName']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <button class="btn btn-primary" type="submit">Load Session</button>
</form>

<!-- Step 2: edit form, pre-filled with current values -->
<?php if ($selectedID > 0 && $sessionInfo): ?>
    <h3>Editing: <?= htmlspecialchars($sessionInfo['sessionName']) ?></h3>
    <div class="form-box" style="margin-top:12px;">
        <form method="post" action="switch_session.php">
            <input type="hidden" name="sessionID" value="<?= $selectedID ?>">

            <div class="form-group">
                <label for="sessionDay">Day <span style="color:#c0392b">*</span></label>
                <select id="sessionDay" name="sessionDay" required>
                    <?php
                    $currentDay = $_POST['sessionDay'] ?? $sessionInfo['sessionDay'];
                    foreach (['Day 1', 'Day 2'] as $d):
                    ?>
                        <option value="<?= $d ?>" <?= $currentDay === $d ? 'selected' : '' ?>>
                            <?= $d ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="startTime">Start Time <span style="color:#c0392b">*</span></label>
                <input type="time" id="startTime" name="startTime" required
                       value="<?= htmlspecialchars(substr($_POST['startTime'] ?? $sessionInfo['startTime'], 0, 5)) ?>">
            </div>

            <div class="form-group">
                <label for="endTime">End Time <span style="color:#c0392b">*</span></label>
                <input type="time" id="endTime" name="endTime" required
                       value="<?= htmlspecialchars(substr($_POST['endTime'] ?? $sessionInfo['endTime'], 0, 5)) ?>">
            </div>

            <div class="form-group">
                <label for="roomLocation">Room Location <span style="color:#c0392b">*</span></label>
                <input type="text" id="roomLocation" name="roomLocation" required
                       value="<?= htmlspecialchars($_POST['roomLocation'] ?? $sessionInfo['roomLocation']) ?>">
                <span class="hint">e.g. Main Hall, Room A, Room B</span>
            </div>

            <button class="btn btn-primary" type="submit" name="update" value="1">Save Changes</button>
            <a class="btn btn-secondary" href="switch_session.php" style="margin-left:8px;">Cancel</a>
        </form>
    </div>
<?php endif; ?>

<?php page_footer(); ?>
