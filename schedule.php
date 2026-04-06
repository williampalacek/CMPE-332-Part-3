<?php
require_once 'config.php';
require_once 'includes.php';
page_header('Conference Schedule', 'schedule');

$selectedDay = isset($_GET['day']) ? $_GET['day'] : '';
$sessions    = [];

$allowedDays = ['Day 1', 'Day 2'];

if (in_array($selectedDay, $allowedDays)) {
    // Fetch sessions for the chosen day, grouped by time slot; include speakers
    $stmt = $pdo->prepare(
        "SELECT s.sessionID, s.sessionName, s.startTime, s.endTime, s.roomLocation,
                GROUP_CONCAT(CONCAT(a.firstName, ' ', a.lastName) ORDER BY a.lastName SEPARATOR ', ') AS speakers
         FROM Session s
         LEFT JOIN Speaks sp ON sp.sessionID = s.sessionID
         LEFT JOIN Attendee a ON a.attendeeID = sp.attendeeID
         WHERE s.sessionDay = ?
         GROUP BY s.sessionID
         ORDER BY s.startTime, s.roomLocation"
    );
    $stmt->execute([$selectedDay]);
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<form method="get" action="schedule.php">
    <div class="form-group" style="max-width:240px;">
        <label for="day">Select Conference Day</label>
        <select name="day" id="day">
            <option value="">-- Choose a day --</option>
            <option value="Day 1" <?= $selectedDay === 'Day 1' ? 'selected' : '' ?>>Day 1</option>
            <option value="Day 2" <?= $selectedDay === 'Day 2' ? 'selected' : '' ?>>Day 2</option>
        </select>
    </div>
    <button class="btn btn-primary" type="submit">View Schedule</button>
</form>

<?php if ($selectedDay !== ''): ?>
    <h3>Schedule for <?= htmlspecialchars($selectedDay) ?></h3>

    <?php if (empty($sessions)): ?>
        <p class="alert alert-info">No sessions scheduled for <?= htmlspecialchars($selectedDay) ?>.</p>
    <?php else: ?>
        <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Start</th>
                    <th>End</th>
                    <th>Session</th>
                    <th>Location</th>
                    <th>Speaker(s)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sessions as $s): ?>
                <tr>
                    <td><?= htmlspecialchars(date('g:i A', strtotime($s['startTime']))) ?></td>
                    <td><?= htmlspecialchars(date('g:i A', strtotime($s['endTime']))) ?></td>
                    <td><?= htmlspecialchars($s['sessionName']) ?></td>
                    <td><?= htmlspecialchars($s['roomLocation']) ?></td>
                    <td><?= $s['speakers'] ? htmlspecialchars($s['speakers']) : '<em>TBA</em>' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php page_footer(); ?>
