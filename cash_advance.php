<?php 
include 'includes/header.php'; 
require 'config.php';

// Fetch cash advances with user names
$stmt = $pdo->query("SELECT ca.*, u.full_name FROM cash_advance ca JOIN users u ON ca.user_id = u.id ORDER BY ca.ca_date DESC");
$cash_advances = $stmt->fetchAll();

// Fetch users for Add modal dropdown only
$users = $pdo->query("SELECT id, full_name FROM users ORDER BY full_name ASC")->fetchAll();
?>

<!-- Bootstrap CSS CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

<style>
  /* Your existing CSS here (same as before) */
  body {
    background: url('uploads/bg.jpg') no-repeat center center fixed;
    background-size: cover;
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
  }
  .main-content {
    padding: 40px;
  }
  .card {
    background: rgba(255, 255, 255, 0.85);
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    padding: 30px;
    backdrop-filter: blur(8px);
  }
  h2 {
    font-size: 28px;
    font-weight: 600;
    margin-bottom: 25px;
    color: #333;
  }
  .btn-add {
    font-size: 16px;
    padding: 10px 20px;
    margin-bottom: 25px;
    background-color: #007bff;
    color: white;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    transition: background 0.3s;
  }
  .btn-add:hover {
    background-color: #0056b3;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    background-color: rgba(255,255,255,0.7);
    backdrop-filter: blur(4px);
  }
  table th, table td {
    padding: 14px 18px;
    border: 1px solid #dee2e6;
    vertical-align: middle !important;
  }
  table thead th {
    background-color: rgba(52,58,64,0.9);
    color: white;
    text-align: left;
  }
  table tbody tr:hover {
    background-color: rgba(241,243,245,0.6);
  }
  .btn-sm {
    padding: 5px 10px;
    font-size: 14px;
  }
  .btn-warning {
    background-color: #ffc107;
    border: none;
    color: #212529;
    border-radius: 4px;
    cursor: pointer;
  }
  .btn-danger {
    background-color: #dc3545;
    border: none;
    color: white;
    border-radius: 4px;
    cursor: pointer;
  }
  .btn-warning:hover {
    background-color: #e0a800;
  }
  .btn-danger:hover {
    background-color: #c82333;
  }
</style>

<div class="main-content">
  <div class="card">
    <h2>💸 Cash Advances</h2>

    <!-- Add button triggers Add Modal -->
    <button type="button" class="btn-add" data-bs-toggle="modal" data-bs-target="#addCashAdvanceModal">
      ➕ Add Cash Advance
    </button>

    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Employee ID</th>
            <th>Name</th>
            <th>Amount</th>
            <th>Tools</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cash_advances as $ca): ?>
            <tr>
              <td><?= htmlspecialchars($ca['ca_date']) ?></td>
              <td><?= htmlspecialchars($ca['user_id']) ?></td>
              <td><?= htmlspecialchars($ca['full_name']) ?></td>
              <td>₱<?= number_format($ca['amount'], 2) ?></td>
              <td>
                <button 
                  class="btn btn-sm btn-warning edit-btn" 
                  data-id="<?= $ca['id'] ?>"
                  data-bs-toggle="modal" 
                  data-bs-target="#editCashAdvanceModal"
                >
                  Edit
                </button>
                <a href="delete_cash_advance.php?id=<?= $ca['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this cash advance?')">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Add Cash Advance Modal -->
<div class="modal fade" id="addCashAdvanceModal" tabindex="-1" aria-labelledby="addCashAdvanceLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="add_cash_advance.php" method="POST" id="addCashAdvanceForm">
        <div class="modal-header">
          <h5 class="modal-title" id="addCashAdvanceLabel">➕ New Cash Advance</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <label>Date:</label>
          <input type="date" name="ca_date" required class="form-control mb-2">

          <label>Employee:</label>
          <select name="user_id" class="form-control mb-2" required>
            <option value="">Select employee</option>
            <?php foreach ($users as $u): ?>
              <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['full_name']) ?></option>
            <?php endforeach; ?>
          </select>

          <label>Amount:</label>
          <input type="number" name="amount" step="0.01" required class="form-control mb-2">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">💾 Save</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Cash Advance Modal -->
<div class="modal fade" id="editCashAdvanceModal" tabindex="-1" aria-labelledby="editCashAdvanceLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editCashAdvanceForm" method="POST" action="edit_cash_advance.php">
        <input type="hidden" name="id" id="edit_id">
        <div class="modal-header">
          <h5 class="modal-title" id="editCashAdvanceLabel">✏️ Edit Cash Advance</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <label>Date:</label>
          <input type="date" name="ca_date" id="edit_ca_date" class="form-control mb-2" required>

          <!-- Employee field removed -->

          <label>Amount:</label>
          <input type="number" name="amount" id="edit_amount" step="0.01" class="form-control mb-2" required>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">💾 Update</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Preload all cash advance data into JS object for editing without AJAX
  const cashAdvances = <?= json_encode($cash_advances) ?>;

  document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', () => {
      const id = button.getAttribute('data-id');
      const cash = cashAdvances.find(c => c.id == id);

      if (!cash) {
        alert('Failed to load cash advance data.');
        return;
      }

      document.getElementById('edit_id').value = cash.id;
      document.getElementById('edit_ca_date').value = cash.ca_date;
      document.getElementById('edit_amount').value = cash.amount;
    });
  });
</script>
