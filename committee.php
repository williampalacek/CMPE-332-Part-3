<?php
require_once 'config.php';
require_once 'includes.php';
page_header('Organizing Sub-Committees', 'committee');

// Fetch all sub-committees for the dropdown
$committees = $pdo->query("SELECT committeeID, committeeName FROM SubCommittee ORDER BY committeeName")
                   ->fetchAll(PDO::FETCH_ASSOC);

$selected    = isset($_GET['committeeID']) ? (int)$_GET['committeeID'] : 0;
$members     = [];
$committeeName = '';

if ($selected > 0) {
    // Look up the committee name
    $stmt = $pdo->prepare("SELECT committeeName FROM SubCommittee WHERE committeeID = ?");
    $stmt->execute([$selected]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $committeeName = $row['committeeName'];
    }

    // Fetch members; chair first, then alphabetical
    $stmt = $pdo->prepare(
        "SELECT cm.firstName, cm.lastName, cm.email, m.isChair
         FROM Membership m
         JOIN CommitteeMember cm ON cm.memberID = m.memberID
         WHERE m.committeeID = ?
         ORDER BY m.isChair DESC, cm.lastName, cm.firstName"
    );
    $stmt->execute([$selected]);
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<form method="get" action="committee.php">
    <div class="form-group" style="max-width:380px;">
        <label for="committeeID">Select Sub-Committee</label>
        <select name="committeeID" id="committeeID">
            <option value="">-- Choose a committee --</option>
            <?php foreach ($committees as $c): ?>
                <option value="<?= $c['committeeID'] ?>"
                    <?= $selected === (int)$c['committeeID'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['committeeName']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <button class="btn btn-primary" type="submit">View Members</button>
</form>

<?php if ($selected > 0): ?>
    <h3><?= htmlspecialchars($committeeName) ?> &mdash; Members</h3>

    <?php if (empty($members)): ?>
        <p class="alert alert-info">No members found for this committee.</p>
    <?php else: ?>
        <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($members as $m): ?>
                <tr>
                    <td><?= htmlspecialchars($m['firstName']) ?></td>
                    <td><?= htmlspecialchars($m['lastName']) ?></td>
                    <td><?= htmlspecialchars($m['email']) ?></td>
                    <td>
                        <?php if ($m['isChair']): ?>
                            <span class="badge badge-chair">Chair</span>
                        <?php else: ?>
                            Member
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php page_footer(); ?>
