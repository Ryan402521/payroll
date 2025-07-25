<?php
session_start();
require 'config.php';

$role = $_SESSION['user']['role'] ?? '';
$user_id = $_SESSION['user']['id'] ?? '';

if ($role === 'admin') {
    $stmt = $pdo->query("SELECT a.*, u.full_name FROM attendance_logs a JOIN users u ON a.user_id = u.id ORDER BY date DESC");
} else {
    $stmt = $pdo->prepare("SELECT a.*, u.full_name FROM attendance_logs a JOIN users u ON a.user_id = u.id WHERE a.user_id = ? ORDER BY date DESC");
    $stmt->execute([$user_id]);
}

$logs = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<style>
  body {
    background: url('uploads/bg.jpg') no-repeat center center fixed;
    background-size: cover;
    margin: 0;
    font-family: Arial, sans-serif;
  }

  .main-content {
    padding: 30px;
    margin: 30px;
    border-radius: 15px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    color: #000;
    transition: margin-left 0.3s ease;
  }

  body.sidebar-open .main-content {
    margin-left: 270px;
  }

  h2 {
    margin-bottom: 20px;
    color: #fff;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.4);
  }

  table {
    width: 100%;
    border-collapse: collapse;
    background: rgba(255, 255, 255, 0.85);
    border-radius: 10px;
    overflow: hidden;
  }

  thead {
    background-color: rgba(0, 102, 204, 0.9);
    color: white;
  }

  th, td {
    padding: 12px 15px;
    border: 1px solid #ccc;
    text-align: left;
  }

  tbody tr:nth-child(even) {
    background-color: rgba(255, 255, 255, 0.6);
  }

  tbody tr:hover {
    background-color: rgba(217, 232, 255, 0.8);
  }

  .action-btn {
    padding: 6px 12px;
    margin-right: 8px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
  }

  .edit-btn {
    background-color: #4caf50;
    color: white;
  }

  .edit-btn:hover {
    background-color: #45a049;
  }

  .delete-btn {
    background-color: #f44336;
    color: white;
  }

  .delete-btn:hover {
    background-color: #da190b;
  }

  .modal {
    display: none;
    position: fixed;
    z-index: 999;
    left: 0; top: 0;
    width: 100%; height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    justify-content: center;
    align-items: center;
  }

  .modal-content {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    padding: 30px;
    border-radius: 12px;
    width: 400px;
    position: relative;
    box-shadow: 0 8px 32px rgba(0,0,0,0.3);
  }

  .modal-content h3 {
    margin-top: 0;
    text-align: center;
  }

  .modal-content label {
    display: block;
    margin-top: 15px;
    font-weight: bold;
  }

  .modal-content input {
    width: 100%;
    padding: 10px;
    margin-top: 6px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 15px;
  }

  .modal-content button {
    margin-top: 25px;
    padding: 10px 20px;
    background: #007BFF;
    color: #fff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    width: 100%;
    font-size: 16px;
    font-weight: bold;
  }

  .modal-content button:hover {
    background-color: #0056b3;
  }

  .close-modal {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 24px;
    cursor: pointer;
    font-weight: bold;
    color: #333;
  }
</style>

<div class="main-content">
  <h2>Attendance Logs</h2>
  <table>
    <thead>
      <tr>
        <th>Full Name</th>
        <th>Date</th>
        <th>Time In</th>
        <th>Time Out</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($logs as $log): ?>
      <tr>
        <td><?= htmlspecialchars($log['full_name']) ?></td>
        <td><?= htmlspecialchars($log['date']) ?></td>
        <td><?= htmlspecialchars($log['time_in']) ?></td>
        <td><?= htmlspecialchars($log['time_out']) ?></td>
        <td>
          <button
            class="action-btn edit-btn"
            onclick="openEditModal('<?= $log['id'] ?>', '<?= $log['date'] ?>', '<?= $log['time_in'] ?>', '<?= $log['time_out'] ?>')"
          >Edit</button>
          <a href="delete_attendance.php?id=<?= $log['id'] ?>" onclick="return confirm('Delete this log?');" class="action-btn delete-btn">Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- EDIT MODAL -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <span class="close-modal" onclick="closeEditModal()">&times;</span>
    <h3>Edit Attendance Log</h3>
    <form action="edit_attendance.php" method="POST">
      <input type="hidden" name="id" id="edit_id">

      <label for="edit_date">Date:</label>
      <input type="date" name="date" id="edit_date" required>

      <label for="edit_time_in">Time In:</label>
      <input type="datetime-local" name="time_in" id="edit_time_in" required>

      <label for="edit_time_out">Time Out:</label>
      <input type="datetime-local" name="time_out" id="edit_time_out" required>

      <button type="submit">💾 Save</button>
    </form>
  </div>
</div>

<!-- ✅ FIXED SCRIPT -->
<script>
  function openEditModal(id, date, timeIn, timeOut) {
    function formatForInput(datetime) {
      const d = new Date(datetime);
      if (isNaN(d)) return ''; // fallback
      const pad = n => n.toString().padStart(2, '0');
      const year = d.getFullYear();
      const month = pad(d.getMonth() + 1);
      const day = pad(d.getDate());
      const hour = pad(d.getHours());
      const minute = pad(d.getMinutes());
      return `${year}-${month}-${day}T${hour}:${minute}`;
    }

    document.getElementById('edit_id').value = id;
    document.getElementById('edit_date').value = date;
    document.getElementById('edit_time_in').value = formatForInput(timeIn);
    document.getElementById('edit_time_out').value = formatForInput(timeOut);
    document.getElementById('editModal').style.display = 'flex';
  }

  function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
  }

  window.onclick = function(e) {
    if (e.target === document.getElementById('editModal')) {
      closeEditModal();
    }
  };
</script>
