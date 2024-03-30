<?php
$kind     = array();
$kind[1]  = '質問';
$kind[2]  = 'ご意見';
$kind[3]  = '資料請求';
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
  } else if( mb_strlen($_POST['fullname']) > 100 ){
    $errormessage[] = "名前は100文字以内にしてください";
  }
  $_SESSION['fullname']	= htmlspecialchars($_POST['fullname'], ENT_QUOTES);

  if( !$_POST['email'] ) {
    $errormessage[] = "Eメールを入力してください";
  } else if( mb_strlen($_POST['email']) > 200 ){
    $errormessage[] = "Eメールは200文字以内にしてください";
  } else if( !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ){
    $errormessage[] = "メールアドレスが不正です";
  }
  $_SESSION['email']	= htmlspecialchars($_POST['email'], ENT_QUOTES);

  if( !$_POST['mkind'] ) {
    $errormessage[] = "種別を入力してください";
  } else if( $_POST['mkind'] <= 0 || $_POST['mkind'] >= 4 ){
    $errormessage[] = "種別が不正です";
  }
  $_SESSION['mkind']	= htmlspecialchars($_POST['mkind'], ENT_QUOTES);

  if( !isset($_POST['present']) || !$_POST['present'] ) {
    $errormessage[] = "プレゼントを選んでください";
  } else if( $_POST['present'] <= 0 || $_POST['present'] >= 4 ){
    $errormessage[] = "プレゼントが不正です";
  }
  if( isset($_POST['present']) ){
    $_SESSION['present']	= htmlspecialchars($_POST['present'], ENT_QUOTES);
  }

  if( isset($_POST['info1']) && mb_strlen($_POST['info1']) > 100 ) {
    $errormessage[] = "メールマガジン情報1が不正です";
  }
  if( isset($_POST['info1']) ){
    $_SESSION['info1']	= htmlspecialchars($_POST['info1'], ENT_QUOTES);
  } else {
    $_SESSION['info1']	= "";
  }

  if( isset($_POST['info2']) && mb_strlen($_POST['info2']) > 100 ) {
    $errormessage[] = "メールマガジン情報2が不正です";
  }
  if( isset($_POST['info2']) ){
    $_SESSION['info2']	= htmlspecialchars($_POST['info2'], ENT_QUOTES);
  } else {
    $_SESSION['info2']	= "";
  }

  if( isset($_POST['info3']) && mb_strlen($_POST['info3']) > 100 ) {
    $errormessage[] = "メールマガジン情報3が不正です";
  }
  if( isset($_POST['info3']) ){
    $_SESSION['info3']	= htmlspecialchars($_POST['info3'], ENT_QUOTES);
  } else {
    $_SESSION['info3']	= "";
  }
  
  if( !$_POST['message'] ){
    $errormessage[] = "お問い合わせ内容を入力してください";
  } else if( mb_strlen($_POST['message']) > 500 ){
    $errormessage[] = "お問い合わせ内容は500文字以内にしてください";
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
    $message = "お問い合わせを受け付けました \r\n"
           . "名前: " . $_SESSION['fullname'] . "\r\n"
           . "email: " . $_SESSION['email'] . "\r\n"
           . "種別: " . $kind[ $_SESSION['mkind'] ] . "\r\n"
           . "プレゼント: " . $present[ $_SESSION['present'] ] . "\r\n"
           . "メールマガジン: \r\n"
           . "" . $_SESSION['info1']. "\r\n"
           . "" . $_SESSION['info2']. "\r\n"
           . "" . $_SESSION['info3']. "\r\n"
           . "お問い合わせ内容:\r\n"
           . preg_replace( "/\r\n|\r|\n/", "\r\n", $_SESSION['message'] );
    mail( $_SESSION['email'], 'お問い合わせありがとうございます', $message );
    mail( 'uemura@hoge.com', 'お問い合わせありがとうございます', $message );
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
  <title>お問い合わせフォーム</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
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
  <form action="contactform.php" method="post">
    名前    <input type="text"    name="fullname" value="<?php echo $_SESSION['fullname'] ?>"  class="form-control"><br>
    Eメール <input type="email"   name="email"    value="<?php echo $_SESSION['email'] ?>"  class="form-control"><br>
    種別:
    <select name="mkind" class="form-control">
    <?php foreach( $kind as $i => $v ){ ?>
      <?php if( $_SESSION['mkind'] == $i ) { ?>
            <option value="<?php echo $i ?>" selected><?php echo $v ?></option>
      <?php } else { ?>
            <option value="<?php echo $i ?>" ><?php echo $v ?></option>
      <?php } ?>
    <?php } ?>
    </select><br>
    アンケートに回答してくれた方にはもれなくプレゼント！<br>
    一つお選びください。<br>
    <?php foreach( $present as $i => $v ){ ?>
      <?php if( $_SESSION['present'] == $i ){ ?>
        <label><input type="radio" name="present" value="<?php echo $i ?>" checked><?php echo $v ?></label><br>
      <?php } else { ?>
        <label><input type="radio" name="present" value="<?php echo $i ?>" ><?php echo $v ?></label><br>
      <?php } ?>
    <?php } ?>
    横浜でペットとよく行く場所はどこですか<br>
    <label><input type="checkbox" name="info1" value="みなとみらい" checked>みなとみらい</label><br>
    <label><input type="checkbox" name="info2" value="中華街" checked>中華街</label><br>
    <label><input type="checkbox" name="info3" value="桜木町" checked>桜木町</label><br>
    お問い合わせ内容<br>
    <textarea cols="40" rows="8" name="message"  class="form-control"><?php echo $_SESSION['message'] ?></textarea><br>
    <div class="button">
      <input type="submit" name="confirm" value="確認" class="btn btn-primary btn-lg"/>
    </div>
  </form>
<?php } else if( $mode == 'confirm' ){ ?>
  <!-- 確認画面 -->
  <form action="contactform.php" method="post">
    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
    名前    <?php echo $_SESSION['fullname'] ?><br>
    Eメール <?php echo $_SESSION['email'] ?><br>
    種別    <?php echo $kind[ $_SESSION['mkind'] ] ?><br>
    プレゼント    <?php echo $present[ $_SESSION['present'] ] ?><br>
    メールマガジン<br>
    <?php echo $_SESSION['info1'] ?><br>
    <?php echo $_SESSION['info2'] ?><br>
    <?php echo $_SESSION['info3'] ?><br>
    お問い合わせ内容<br>
    <?php echo nl2br($_SESSION['message']) ?><br>
    <input type="submit" name="back" value="戻る" class="btn btn-primary btn-lg"/>
    <input type="submit" name="send" value="送信" class="btn btn-primary btn-lg"/>
  </form>

<?php } else { ?>
  <!-- 完了画面 -->
  送信しました。お問い合わせありがとうございました。<br>
<?php } ?>
</body>
</html>


