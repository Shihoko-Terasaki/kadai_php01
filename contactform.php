<?php
$kind     = array();
$kind[1]  = '犬';
$kind[2]  = '猫';
$kind[3]  = 'その他のペット';
$present  = array();
$present[1]  = 'チャオチュール';
$present[2]  = '馬肉ふりかけ';
$present[3]  = 'LEDお散歩ライト';
session_start();
$mode = 'input';
$errormessage = array();
if( isset($_POST['back']) && $_POST['back'] ){
  // 何もしない
} else if( isset($_POST['confirm']) && $_POST['confirm'] ){
  // 確認画面
  if( !$_POST['fullname'] ) {
    $errormessage[] = "名前を入力してください";
  } else if( mb_strlen($_POST['fullname']) > 10 ){
    $errormessage[] = "名前は100文字以内にしてください";
  }
  $_SESSION['fullname']	= htmlspecialchars($_POST['fullname'], ENT_QUOTES);

  if( !$_POST['email'] ) {
    $errormessage[] = "メールアドレスを入力してください";
  } else if( mb_strlen($_POST['email']) > 200 ){
    $errormessage[] = "メールアドレスは200文字以内にしてください";
  } else if( !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ){
    $errormessage[] = "メールアドレスを再度ご確認ください";
  }
  $_SESSION['email']	= htmlspecialchars($_POST['email'], ENT_QUOTES);

  if( !$_POST['mkind'] ) {
    $errormessage[] = "ペットの種類を入力してください";
  } else if( $_POST['mkind'] <= 0 || $_POST['mkind'] >= 4 ){
    $errormessage[] = "いづれかを選択してください。";
  }
  $_SESSION['mkind']	= htmlspecialchars($_POST['mkind'], ENT_QUOTES);

  if( !isset($_POST['present']) || !$_POST['present'] ) {
    $errormessage[] = "プレゼントを選んでください";
  } else if( $_POST['present'] <= 0 || $_POST['present'] >= 4 ){
    $errormessage[] = "プレゼントを一つ選んでください";
  }
  if( isset($_POST['present']) ){
    $_SESSION['present']	= htmlspecialchars($_POST['present'], ENT_QUOTES);
  }

  if( isset($_POST['info1']) && mb_strlen($_POST['info1']) > 100 ) {
    $errormessage[] = "情報が不正です";
  }
  if( isset($_POST['info1']) ){
    $_SESSION['info1']	= htmlspecialchars($_POST['info1'], ENT_QUOTES);
  } else {
    $_SESSION['info1']	= "";
  }

  if( isset($_POST['info2']) && mb_strlen($_POST['info2']) > 100 ) {
    $errormessage[] = "情報が不正です";
  }
  if( isset($_POST['info2']) ){
    $_SESSION['info2']	= htmlspecialchars($_POST['info2'], ENT_QUOTES);
  } else {
    $_SESSION['info2']	= "";
  }

  if( isset($_POST['info3']) && mb_strlen($_POST['info3']) > 100 ) {
    $errormessage[] = "情報が不正です";
  }
  if( isset($_POST['info3']) ){
    $_SESSION['info3']	= htmlspecialchars($_POST['info3'], ENT_QUOTES);
  } else {
    $_SESSION['info3']	= "";
  }
  
  if( !$_POST['message'] ){
    $errormessage[] = "本日の感想を入力してください";
  } else if( mb_strlen($_POST['message']) > 10 ){
    $errormessage[] = "感想は500文字以内にしてください";
  }
  $_SESSION['message'] = htmlspecialchars($_POST['message'], ENT_QUOTES);

  if( $errormessage ){
    $mode = 'input';

  } else {
    $token = bin2hex(random_bytes(32)); 
    $_SESSION['token']  = $token;
    $mode = 'confirm';
  }
} else if( isset($_POST['send']) && $_POST['send'] ){
  // 送信ボタンを押したとき
  if( !$_POST['token'] || !$_SESSION['token'] || !$_SESSION['email'] ){
    $errormessage[] = '不正な処理が行われました';
    $_SESSION     = array();
    $mode         = 'input';
  } else if( $_POST['token'] != $_SESSION['token'] ){
    $errormessage[] = '不正な処理が行われました';
    $_SESSION     = array();
    $mode         = 'input';
  } else {
    $message = "アンケートを受け付けました。ご協力ありがとうございました。  \r\n"
           . "お名前: " . $_SESSION['fullname'] . "\r\n"
           . "メールアドレス: " . $_SESSION['email'] . "\r\n"
           . "種類: " . $kind[ $_SESSION['mkind'] ] . "\r\n"
           . "プレゼント: " . $present[ $_SESSION['present'] ] . "\r\n"
           . "ペットの種類: \r\n"
           . "" . $_SESSION['info1']. "\r\n"
           . "" . $_SESSION['info2']. "\r\n"
           . "" . $_SESSION['info3']. "\r\n"
           . "感想:\r\n"
           . preg_replace( "/\r\n|\r|\n/", "\r\n", $_SESSION['message'] );
    mail( $_SESSION['email'], 'アンケートのご協力ありがとうございました。', $message );
    mail( 'shihoko.com@gmail.com', 'アンケート結果', $message );
    $_SESSION = array();
    $mode     = 'send';
  }
} else {
  $_SESSION['fullname'] = "";
  $_SESSION['email']    = "";
  $_SESSION['mkind']    = "";
  $_SESSION['present']  = "";
  $_SESSION['info1']  = "";
  $_SESSION['info2']  = "";
  $_SESSION['info3']  = "";
  $_SESSION['message']  = "";
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>アンケートフォーム</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <style>
    body{
      padding: 10px;
      max-width: 600px;
      margin: 0px auto;
    }
    div.button{
      text-align: center;
    }
  </style>
</head>
<body>
<?php if( $mode == 'input' ){ ?>
  <!-- 入力画面 -->
  <?php
  if( $errormessage ){
    echo '<div class="alert alert-danger" role="alert">';
    echo implode('<br>', $errormessage );
    echo '</div>';
  }
  ?>
  <p class="h1">アンケートフォーム</p>
  <form action="contactform.php" method="post">
    お名前    <input type="text"    name="fullname" value="<?php echo $_SESSION['fullname'] ?>"  class="form-control"><br>
    メールアドレス <input type="email"   name="email"    value="<?php echo $_SESSION['email'] ?>"  class="form-control"><br>
    ペットの種類
    <select name="mkind" class="form-control">
    <?php foreach( $kind as $i => $v ){ ?>
      <?php if( $_SESSION['mkind'] == $i ) { ?>
            <option value="<?php echo $i ?>" selected><?php echo $v ?></option>
      <?php } else { ?>
            <option value="<?php echo $i ?>" ><?php echo $v ?></option>
      <?php } ?>
    <?php } ?>
    </select><br>
    <p class="h6">アンケートに回答してくれた方には、サンプルをもれなく１つプレゼント！</p>
    <p>
    <?php foreach( $present as $i => $v ){ ?>
      <?php if( $_SESSION['present'] == $i ){ ?>
        <label><input type="radio" name="present" value="<?php echo $i ?>" checked><?php echo $v ?></label><br>
      <?php } else { ?>
        <label><input type="radio" name="present" value="<?php echo $i ?>" ><?php echo $v ?></label><br>
      <?php } ?>
    <?php } ?>
    </p>
    <p class="h6">横浜でペットとよく行く場所はどこですか？（複数選択可）</p>
    <p>
    <label><input type="checkbox" name="info1" value="みなとみらい" checked>みなとみらい</label><br>
    <label><input type="checkbox" name="info2" value="中華街" checked>中華街</label><br>
    <label><input type="checkbox" name="info3" value="その他" checked>その他</label><br>
    </p>
    <p class="h6">本日の感想</p>
    <p>
    <textarea cols="40" rows="8" name="message"  class="form-control"><?php echo $_SESSION['message'] ?></textarea><br>
    </p>
    <div class="button">
      <input type="submit" name="confirm" value="確認" class="btn btn-primary btn-lg"/>
    </div>
  </form>
<?php } else if( $mode == 'confirm' ){ ?>
  <!-- 確認画面 -->
  <p class="h1">内容をご確認ください</p>
  <form action="contactform.php" method="post">
    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
    <p> 名前：<?php echo $_SESSION['fullname'] ?>  </p>
    <p>メールアドレス：<?php echo $_SESSION['email'] ?>  </p>
    <p>ペットの種類：<?php echo $kind[ $_SESSION['mkind'] ] ?>  </p>
    <p>プレゼント：<?php echo $present[ $_SESSION['present'] ] ?>  </p>
    <p>よく行く場所：</br>
    <li><?php echo $_SESSION['info1'] ?></li>
    <li><?php echo $_SESSION['info2'] ?></li>
    <li><?php echo $_SESSION['info3'] ?></li>
    </p>
    <p> 本日の感想：</br>
    <?php echo nl2br($_SESSION['message']) ?></p>

    <input type="submit" name="back" value="戻る" class="btn btn-primary btn-lg"/>
    <input type="submit" name="send" value="送信" class="btn btn-primary btn-lg"/>
  </form>
<?php } else { ?>
  <!-- 完了画面 -->
  <p class="h1">送信しました。ご協力ありがとうございました。</p>
<?php } ?>

</body>
</html>


