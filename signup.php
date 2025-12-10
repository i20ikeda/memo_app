<?php
include 'config.php';
//登録ボタンが押されたら、処理開始
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  //ユーザ名を受け取る
  $username = $_POST["username"];
  //パスワードをハッシュ化
  $password = password_hash('__________');
  //SQLの準備
  $stmt = $conn->prepare('__________');
  //パラメータ(ユーザ名、パスワード)を埋め込む
  $stmt->bind_param(('__________'));
  //成功したら、ログイン画面に遷移
  if ($stmt->execute()) header("Location: index.php");
  else $error = "登録に失敗しました。";
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>新規登録 | メモアプリ</title>
<link rel="stylesheet" href="style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <div class="card mx-auto p-4" style="max-width:400px;">
    <h4 class="text-center mb-3">新規登録</h4>
    <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <form method="post">
      <div class="mb-3">
        <label class="form-label">ユーザー名</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">パスワード</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button class="btn btn-success w-100">登録</button>
    </form>
  </div>
</div>
</body>
</html>