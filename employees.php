<?php include 'includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee List</title>

    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: url('uploads/bg.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        .main-content {
            padding: 30px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }

        .main-content-inner {
            background-color: rgba(255, 255, 255, 0.85);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.15);
        }

        body.sidebar-open .main-content {
            margin-left: 270px;
        }

        .main-content h2 {
            margin-bottom: 10px;
            color: #333;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .btn-primary { background-color: #007bff; color: white; }
        .btn-primary:hover { background-color: #0056b3; }
        .btn-secondary { background-color: #6c757d; color: white; }
        .btn-secondary:hover { background-color: #545b62; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-danger:hover { background-color: #b52a37; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background-color: white;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }

        thead { background-color: #0066cc; color: white; }
        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
        }

        tbody tr:hover { background-color: #f1f1f1; }

        .profile-pic {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }

        #edit_image_preview {
            display: block;
            margin: 10px auto;
            border-radius: 50%;
            width: 80px;
            height: 80px;
            object-fit: cover;
        }

        .close {
            float: right;
            font-size: 22px;
            font-weight: bold;
            cursor: pointer;
        }

        #editModal .modal-content {
            max-width: 400px;
            margin: 40px auto;
            padding: 20px;
        }
    </style>
</head>
<body>

<div class="main-content">
    <div class="main-content-inner">
        <h2>Employee List</h2>
        <a href="add_user.php" class="btn btn-primary">➕ New</a>

        <?php
        require 'config.php';

        $stmt = $pdo->query("SELECT u.*, u.position_id, p.title AS position_title FROM users u 
                             LEFT JOIN positions p ON u.position_id = p.id
                             ORDER BY u.full_name ASC");
        $users = $stmt->fetchAll();

        $positionStmt = $pdo->query("SELECT * FROM positions ORDER BY title ASC");
        $positions = $positionStmt->fetchAll();
        ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Profile</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Position</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['id']) ?></td>
                    <td>
                        <img src="uploads/<?= htmlspecialchars($u['profile_pic'] ?: 'default-user.png') ?>" class="profile-pic" alt="Profile">
                    </td>
                    <td><?= htmlspecialchars($u['full_name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['role']) ?></td>
                    <td><?= htmlspecialchars($u['position_title'] ?? 'N/A') ?></td>
                    <td style="display: flex; gap: 8px;">
                        <button class="btn btn-secondary edit-btn"
                            data-id="<?= $u['id'] ?>"
                            data-full_name="<?= htmlspecialchars($u['full_name'], ENT_QUOTES) ?>"
                            data-email="<?= htmlspecialchars($u['email'], ENT_QUOTES) ?>"
                            data-role="<?= htmlspecialchars($u['role'], ENT_QUOTES) ?>"
                            data-position_id="<?= htmlspecialchars($u['position_id'], ENT_QUOTES) ?>"
                            data-profile_pic="<?= htmlspecialchars($u['profile_pic'] ?: 'default-user.png', ENT_QUOTES) ?>">
                            Edit
                        </button>

                        <a href="#"
                           class="btn btn-danger delete-btn"
                           data-id="<?= $u['id'] ?>"
                           data-bs-toggle="modal"
                           data-bs-target="#deleteModal">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ✅ Edit Modal -->
<div id="editModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <form action="update_user.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" id="edit_id">

            <div class="mb-3">
                <label for="edit_full_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" name="full_name" id="edit_full_name" required>
            </div>

            <div class="mb-3">
                <label for="edit_email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="edit_email" required>
            </div>

            <div class="mb-3">
                <label for="edit_role" class="form-label">Role</label>
                <select name="role" id="edit_role" class="form-select" required>
                    <option value="admin">Admin</option>
                    <option value="employee">Employee</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="edit_position" class="form-label">Position</label>
                <select name="position_id" id="edit_position" class="form-select" required>
                    <option value="">-- Select Position --</option>
                    <?php foreach ($positions as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3 text-center">
                <label class="form-label d-block">Current Profile</label>
                <img id="edit_image_preview" src="" alt="Profile Image">
            </div>

            <div class="mb-3">
                <label for="profile_pic" class="form-label">Change Profile Picture</label>
                <input type="file" class="form-control" name="profile_pic" id="profile_pic">
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">💾 Save</button>
            </div>
        </form>
    </div>
</div>

<!-- ✅ Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this user?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <a href="#" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</a>
      </div>
    </div>
  </div>
</div>

<!-- ✅ Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- ✅ Modal Script -->
<script>
function openEditModal(user) {
    document.getElementById('edit_id').value = user.id;
    document.getElementById('edit_full_name').value = user.full_name;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_role').value = user.role;
    document.getElementById('edit_position').value = user.position_id || '';
    document.getElementById('edit_image_preview').src = 'uploads/' + (user.profile_pic || 'default-user.png');
    document.getElementById('editModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('editModal');
    if (event.target == modal) {
        closeEditModal();
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Handle delete buttons
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const userId = this.getAttribute('data-id');
            confirmDeleteBtn.href = `delete_user.php?id=${userId}`;
        });
    });

    // Handle edit buttons using data-* attributes
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const user = {
                id: this.dataset.id,
                full_name: this.dataset.full_name,
                email: this.dataset.email,
                role: this.dataset.role,
                position_id: this.dataset.position_id,
                profile_pic: this.dataset.profile_pic,
            };
            openEditModal(user);
        });
    });
});
</script>

</body>
</html>
