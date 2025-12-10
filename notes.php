<?php
session_start();
include 'config.php';
//ログインしていなければ強制送還
if (!isset($_SESSION["username"])) { 
    __________; 
    exit; 
}

$username = $_SESSION["username"];

// ----- 編集対象の取得（?edit=ID が来たときにフォームを編集モードにする） -----
$edit_mode = false;
$edit_note = null;
if (isset($_GET['edit'])) {
  //編集したいメモのメモIDを取得
  $edit_id = intval($_GET['edit']);
  //DBから編集したいメモだけを取り出す
  $stmt = $conn->prepare('__________');
  //プレースホルダに変数を入れる
  $stmt->bind_param("is", $edit_id, $username);
  $stmt->execute();
  //実行結果を取得
  $res = $stmt->__________();
  //DBから取得したメモ1行を配列として取り出す
  if ($row = $res->__________()) {
   //編集モードのフラグ
    $edit_mode = true;
     //編集対象のメモのデータをフォームに埋め込むために保存
    $edit_note = $row;
  }
  $stmt->close();
}

// ----- 追加／更新／削除の処理 -----
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // 追加ボタンが押された時
  if (isset($_POST['create'])) {
    //タイトル入力を受け取る
    $title = trim($_POST["title"] ?? "");
    //内容を受け取る
    $content = trim($_POST["content"] ?? "");
    //タイトルと内容がちゃんと書かれているか確認
    if ($title !== "" && $content !== "") {
      //notes テーブルに1件追加するSQLを用意
      $stmt = $conn->prepare('__________');
      //値をSQLに埋め込む
      $stmt->bind_param('__________');
      //実行
      $stmt->execute();
      $stmt->close();
    }
    //ページを更新
    header("Location: notes.php"); exit;
  }

  // 更新するボタンが押された時
  if (isset($_POST['update'])) {
    //更新したいメモのメモIDを受け取る
    $id = intval($_POST["id"] ?? 0);
    //タイトルと内容を受け取る
    $title = trim($_POST["title"] ?? "");
    $content = trim($_POST["content"] ?? "");
    //IDが正しい&タイトルと内容が書かれているか確認
    if ('____________________') {
      //1件更新するSQLの準備
      $stmt = $conn->prepare('__________');
      //値を埋め込む
      $stmt->bind_param('__________');
      //更新実行
      $stmt->execute();
      $stmt->close();
    }
    header("Location: notes.php"); exit;
  }

  // 削除ボタンが押されたか確認
  if (isset($_POST['delete_id'])) {
    //削除したいメモのIDを取得
    $delete_id = intval($_POST['delete_id']);
    //IDが正しいときだけ処理を実行
    if ($delete_id > 0) {
      //IDとユーザが一致するメモを取得
      $stmt = $conn->prepare('__________');
      //値を埋め込む
      $stmt->bind_param('__________');
      //実行→削除される
      $stmt->execute();
      $stmt->close();
    }
    header("Location: notes.php"); exit;
  }
}

//ログインしている人のメモだけ、新しい順に取得
$stmt = $conn->prepare('__________');
//プレースホルダにユーザ名をセット
$stmt->bind_param('__________');
$stmt->execute();
//メモ一覧を取得
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
