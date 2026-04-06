<?php
require_once 'config.php';
require_once 'includes.php';
page_header('Hotel Room Occupants', 'hotel');

// Fetch all rooms for the dropdown
$rooms = $pdo->query("SELECT roomNumber, numBeds FROM HotelRoom ORDER BY roomNumber")
             ->fetchAll(PDO::FETCH_ASSOC);

$selected = isset($_GET['roomNumber']) ? $_GET['roomNumber'] : '';
$students = [];

if ($selected !== '') {
    // Validate that the room exists
    $stmt = $pdo->prepare("SELECT roomNumber, numBeds FROM HotelRoom WHERE roomNumber = ?");
    $stmt->execute([$selected]);
    $roomInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($roomInfo) {
        $stmt = $pdo->prepare(
            "SELECT a.firstName, a.lastName, a.email
             FROM Student s
             JOIN Attendee a ON a.attendeeID = s.attendeeID
             WHERE s.roomNumber = ?
             ORDER BY a.lastName, a.firstName"
        );
        $stmt->execute([$selected]);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<form method="get" action="hotel_room.php">
    <div class="form-group" style="max-width:280px;">
        <label for="roomNumber">Select Hotel Room</label>
        <select name="roomNumber" id="roomNumber">
            <option value="">-- Choose a room --</option>
            <?php foreach ($rooms as $r): ?>
                <option value="<?= htmlspecialchars($r['roomNumber']) ?>"
                    <?= $selected === $r['roomNumber'] ? 'selected' : '' ?>>
                    Room <?= htmlspecialchars($r['roomNumber']) ?>
                    (<?= (int)$r['numBeds'] ?> bed<?= $r['numBeds'] > 1 ? 's' : '' ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <button class="btn btn-primary" type="submit">View Occupants</button>
</form>

<?php if ($selected !== ''): ?>
    <?php if (!isset($roomInfo) || !$roomInfo): ?>
        <p class="alert alert-error">Room not found.</p>
    <?php else: ?>
        <h3>Room <?= htmlspecialchars($roomInfo['roomNumber']) ?>
            &mdash; <?= (int)$roomInfo['numBeds'] ?> Bed<?= $roomInfo['numBeds'] > 1 ? 's' : '' ?>
        </h3>

        <?php if (empty($students)): ?>
            <p class="alert alert-info">No students are currently assigned to this room.</p>
        <?php else: ?>
            <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['firstName']) ?></td>
                        <td><?= htmlspecialchars($s['lastName']) ?></td>
                        <td><?= htmlspecialchars($s['email']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>

<?php page_footer(); ?>
