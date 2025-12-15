<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //入力されたユーザネームとパスワードを格納
    $username = $_POST["username"];
    $password = $_POST["password"];

    //指定した username のユーザをDBから探す SQL の準備
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    //SQl処理の実行
    $stmt->execute();
    
    $result = $stmt->get_result();
    //データベースからとってきたユーザ情報を1件取り出す
    if ($row = $result->fetch_assoc()) {
        //入力されたパスワードと DB のハッシュ化パスワードを比較する
        if (password_verify($password, $row["password"])) {
            //ログイン状態を維持するために $_SESSION["username"] にユーザ名を保存
            $_SESSION["username"] = $username;
            //notes.phpへ遷移
            header("Location: notes.php");
            exit;
        } else $error = "パスワードが違います。";
    } else $error = "ユーザーが存在しません。";
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>ログイン | メモアプリ</title>
<link rel="stylesheet" href="style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <div class="card mx-auto p-4" style="max-width:400px;">
    <h4 class="text-center mb-3">ログイン</h4>
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
      <button class="btn btn-primary w-100">ログイン</button>
    </form>
    <a href="signup.php" class="d-block text-center mt-3">新規登録はこちら</a>
  </div>
</div>
</body>
</html>