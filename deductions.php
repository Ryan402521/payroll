<?php
session_start();
require 'config.php';

// Admin access only
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied.");
}

// Fetch all deductions
$stmt = $pdo->query("SELECT * FROM deductions ORDER BY id DESC");
$deductions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Deductions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: url('uploads/bg.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
        }
        .main-content {
            max-width: 900px;
            margin: 40px auto !important; /* Always center horizontally */
            padding: 90px 30px 25px;
            background-color: rgba(255, 255, 255, 0.7);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            position: relative;
            transition: none !important; /* prevent shifting animation */
        }

        /* Override sidebar open styles so main-content stays centered */
        body.sidebar-open .main-content {
            margin: 40px auto !important; /* Keep centered regardless */
            /* Remove any margin-left or width changes */
            width: auto !important;
        }

        h2 {
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }
        .top-btn {
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px 16px;
            border-bottom: 1px solid #e1e1e1;
            text-align: left;
        }
        thead {
            background-color: #f8f9fa;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .action-buttons .btn {
            margin-right: 6px;
        }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="main-content">
    <h2>💸 Deductions</h2>

    <!-- Add Deduction Button triggers modal -->
    <button class="btn btn-primary top-btn" data-bs-toggle="modal" data-bs-target="#addDeductionModal">➕ Add Deduction</button>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Amount (₱)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($deductions) > 0): ?>
            <?php foreach ($deductions as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td><?= number_format($row['amount'], 2) ?></td>
                    <td class="action-buttons">
                        <button
                            class="btn btn-secondary edit-btn"
                            data-id="<?= $row['id'] ?>"
                            data-bs-toggle="modal"
                            data-bs-target="#editDeductionModal"
                        >
                            ✏️ Edit
                        </button>
                        <a class="btn btn-danger" href="delete_deduction.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this deduction?');">🗑️ Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3">No deductions found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add Deduction Modal -->
<div class="modal fade" id="addDeductionModal" tabindex="-1" aria-labelledby="addDeductionLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="edit_deduction.php" method="POST" id="addDeductionForm">
        <input type="hidden" name="action" value="add" />
        <div class="modal-header">
          <h5 class="modal-title" id="addDeductionLabel">➕ Add Deduction</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <label>Description:</label>
          <input type="text" name="description" class="form-control" required />

          <label>Amount (₱):</label>
          <input type="number" name="amount" step="0.01" class="form-control" required />
        </div>
        <div class="modal-footer">
          <button class="btn btn-success" type="submit">Save</button>
          <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Deduction Modal -->
<div class="modal fade" id="editDeductionModal" tabindex="-1" aria-labelledby="editDeductionLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="edit_deduction.php" method="POST" id="editDeductionForm">
        <input type="hidden" name="action" value="edit" />
        <input type="hidden" name="id" id="edit_id" />
        <div class="modal-header">
          <h5 class="modal-title" id="editDeductionLabel">✏️ Edit Deduction</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <label>Description:</label>
          <input type="text" name="description" id="edit_description" class="form-control" required />

          <label>Amount (₱):</label>
          <input type="number" name="amount" id="edit_amount" step="0.01" class="form-control" required />
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary" type="submit">Update</button>
          <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', () => {
        const id = button.getAttribute('data-id');

        fetch(`edit_deduction.php?id=${id}&action=fetch`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_id').value = data.id;
                document.getElementById('edit_description').value = data.description;
                document.getElementById('edit_amount').value = data.amount;
            })
            .catch(() => alert('Failed to load deduction data.'));
    });
});
</script>

</body>
</html>
