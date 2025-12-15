<?php
session_start();
include 'config.php';
if (!isset($_SESSION["username"])) { header("Location: index.php"); exit; }

$username = $_SESSION["username"];

// ----- 編集対象の取得（?edit=ID が来たときにフォームを編集モードにする） -----
$edit_mode = false;
$edit_note = null;
if (isset($_GET['edit'])) {
  $edit_id = intval($_GET['edit']);
  $stmt = $conn->prepare("SELECT id, title, content FROM notes WHERE id=? AND username=?");
  $stmt->bind_param("is", $edit_id, $username);
  $stmt->execute();
  $res = $stmt->get_result();
  if ($row = $res->fetch_assoc()) {
    $edit_mode = true;
    $edit_note = $row;
  }
  $stmt->close();
}

// ----- 追加／更新／削除の処理 -----
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // 追加
  if (isset($_POST['create'])) {
    $title = trim($_POST["title"] ?? "");
    $content = trim($_POST["content"] ?? "");
    if ($title !== "" && $content !== "") {
      $stmt = $conn->prepare("INSERT INTO notes (username, title, content) VALUES (?, ?, ?)");
      $stmt->bind_param("sss", $username, $title, $content);
      $stmt->execute();
      $stmt->close();
    }
    header("Location: notes.php"); exit;
  }

  // 更新
  if (isset($_POST['update'])) {
    $id = intval($_POST["id"] ?? 0);
    $title = trim($_POST["title"] ?? "");
    $content = trim($_POST["content"] ?? "");
    if ($id > 0 && $title !== "" && $content !== "") {
      $stmt = $conn->prepare("UPDATE notes SET title=?, content=? WHERE id=? AND username=?");
      $stmt->bind_param("ssis", $title, $content, $id, $username);
      $stmt->execute();
      $stmt->close();
    }
    header("Location: notes.php"); exit;
  }

  // 削除
  if (isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    if ($delete_id > 0) {
      $stmt = $conn->prepare("DELETE FROM notes WHERE id=? AND username=?");
      $stmt->bind_param("is", $delete_id, $username);
      $stmt->execute();
      $stmt->close();
    }
    header("Location: notes.php"); exit;
  }
}

// ----- 一覧取得 -----
$stmt = $conn->prepare("SELECT id, title, content, created_at FROM notes WHERE username=? ORDER BY created_at DESC");
$stmt->bind_param("s", $username);
$stmt->execute();
$notes = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>メモ一覧 | メモアプリ</title>
<link rel="stylesheet" href="style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <div class="card p-4">
    <h4 class="text-center mb-4">メモ一覧（<?= htmlspecialchars($username) ?>さん）</h4>

    <!-- 追加フォーム or 編集フォーム -->
    <?php if ($edit_mode && $edit_note): ?>
      <div class="alert alert-info mb-3">編集モード：ID <?= (int)$edit_note['id'] ?></div>
      <form method="post" class="mb-4">
        <input type="hidden" name="id" value="<?= (int)$edit_note['id'] ?>">
        <div class="mb-3">
          <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($edit_note['title']) ?>" required>
        </div>
        <div class="mb-3">
          <textarea name="content" class="form-control" rows="3" required><?= htmlspecialchars($edit_note['content']) ?></textarea>
        </div>
        <div class="d-flex gap-2">
          <button name="update" class="btn btn-primary w-100">更新する</button>
          <a href="notes.php" class="btn btn-outline-secondary w-100">キャンセル</a>
        </div>
      </form>
    <?php else: ?>
      <form method="post" class="mb-4">
        <div class="mb-3">
          <input type="text" name="title" class="form-control" placeholder="タイトル" required>
        </div>
        <div class="mb-3">
          <textarea name="content" class="form-control" rows="3" placeholder="内容" required></textarea>
        </div>
        <button name="create" class="btn btn-primary w-100">メモを追加</button>
      </form>
    <?php endif; ?>

    <!-- 一覧 -->
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th style="width:20%">タイトル</th>
          <th style="width:50%">内容</th>
          <th style="width:20%">作成日時</th>
          <th style="width:10%">操作</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $notes->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row["title"]) ?></td>
            <td><?= nl2br(htmlspecialchars($row["content"])) ?></td>
            <td><?= htmlspecialchars($row["created_at"]) ?></td>
            <td>
              <div class="d-flex gap-1">
                <a href="notes.php?edit=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-primary">編集</a>
                <form method="post" onsubmit="return confirm('削除してよろしいですか？');">
                  <input type="hidden" name="delete_id" value="<?= (int)$row['id'] ?>">
                  <button class="btn btn-sm btn-outline-danger">削除</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <a href="logout.php" class="btn btn-secondary w-100 mt-3">ログアウト</a>
  </div>
</div>
</body>
</html>
