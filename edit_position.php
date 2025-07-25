<?php include 'includes/header.php'; ?>
<div class="main-content">
    <h2>👥 Employee List</h2>
    <p><a href="dashboard.php">⬅ Back to Dashboard</a></p>
    <p><a href="add_user.php" class="btn btn-primary">➕ Add Employee</a></p>

    <?php
    require 'config.php';
    $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll();
    ?>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>👤 Name</th>
                <th>📧 Email</th>
                <th>⚙️ Role</th>
                <th>🛠️ Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['full_name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td>
                    <!-- Edit Button (triggers modal) -->
                    <button class="btn btn-sm btn-info editBtn"
                        data-id="<?= $user['id'] ?>"
                        data-name="<?= htmlspecialchars($user['full_name']) ?>"
                        data-email="<?= htmlspecialchars($user['email']) ?>"
                        data-role="<?= $user['role'] ?>">
                        ✏️ Edit
                    </button>

                    <!-- Delete Button -->
                    <a href="delete_user.php?id=<?= $user['id'] ?>" 
                       class="btn btn-sm btn-danger" 
                       onclick="return confirm('Are you sure you want to delete this employee?')">
                       🗑️ Delete
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- 🧩 Bootstrap Modal for Edit -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="update_user.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Employee</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" id="edit-id">
            <div class="mb-3">
                <label for="edit-name" class="form-label">Full Name</label>
                <input type="text" class="form-control" name="name" id="edit-name" required>
            </div>
            <div class="mb-3">
                <label for="edit-email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="edit-email" required>
            </div>
            <div class="mb-3">
                <label for="edit-role" class="form-label">Role</label>
                <select class="form-control" name="role" id="edit-role" required>
                    <option value="admin">Admin</option>
                    <option value="employee">Employee</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">💾 Save Changes</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Bootstrap JS and Edit Script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.editBtn').forEach(button => {
    button.addEventListener('click', function () {
        document.getElementById('edit-id').value = this.dataset.id;
        document.getElementById('edit-name').value = this.dataset.name;
        document.getElementById('edit-email').value = this.dataset.email;
        document.getElementById('edit-role').value = this.dataset.role;

        // Show Bootstrap modal
        const editModal = new bootstrap.Modal(document.getElementById('editModal'));
        editModal.show();
    });
});
</script>


