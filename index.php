<?php 

date_default_timezone_set("Asia/Tokyo");

$comment_array = array();
$error_array = array();

//DB接続 
try {
  $pdo = new PDO('mysql:host=localhost;dbname=bbs', "root", "");
} catch (PDOException $e) {
    echo $e->getMessage();
}

if (empty($_POST["username"])){
  $error_array["name_error"] = "名前を入力してください";
}

if (empty($_POST["comment"])){
  $error_array["comment_error"] = "コメントを入力してください";
}

//フォームを打ち込んだとき
if (!empty($_POST["submitButton"])){
  if (empty($error_array["name_error"]) && empty($error_array["comment_error"])){
    $postDate = date("Y-m-d H:i:s");

    try {
      //SQL作成
      $statement = $pdo->prepare("INSERT INTO `bbs_table` (username, comment, postDate) VALUES (:username, :comment, :postDate)");

      //値をセット
      $statement->bindParam(':username', $_POST["username"], PDO::PARAM_STR);
      $statement->bindParam(':comment', $_POST["comment"], PDO::PARAM_STR);
      $statement->bindParam(':postDate', $postDate, PDO::PARAM_STR);

      //SQLクエリの実行
      $statement->execute();

  } catch (PDOException $e) {
    echo $e->getMessage();
    }
  }
}

//クエリ
$sql = "SELECT * FROM `bbs_table`;";
$comment_array = $pdo->query($sql);

//DBの接続を閉じる
$pdo = null;

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PHP掲示板</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1 class="title">PHPで掲示板アプリ</h1>
  <hr>
  <div class="boardWrapper">
    <section>
      <?php foreach($comment_array as $comment): ?>
        <article>
          <div class=""wrapper>
            <div class="nameArea">
              <span>名前：</span>
              <p class="username"><?php echo $comment["username"]; ?></p>
              <time>:<?php echo $comment["postDate"]; ?></time>
            </div>
            <p class="comment"><?php echo $comment["comment"]; ?></p>
          </div>
        </article>
      <?php endforeach; ?>
    </section>
    <form class="formWrapper" method="POST">
    <?php 
      if(isset($error_array["name_error"]) && isset($error_array["comment_error"])){
        echo '<span style="color:#FF0000;">名前とコメントを入力してください</span>';
      } 
      elseif(isset($error_array["name_error"])){
        echo '<span style="color:#FF0000;">名前を入力してください</span>';
      }
      elseif(isset($error_array["comment_error"])){
        echo '<span style="color:#FF0000;">コメントを入力してください</span>';
      }
    ?>
      <div>
        <input type="submit" value="書き込む" name="submitButton">
        <label for="">名前:</label>
        <input type="text" name="username">
      </div>
      <div>
        <textarea class="commentTextArea" name="comment"></textarea>
      </div>
    </form>
  </div>
</body>
</html>