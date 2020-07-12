<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mission5-1</title>
</head>
<body>
    


<?php
    //データベース接続
    $dsn='データベース名';
    $user='ユーザー名';
    $password='パスワード';
    $pdo=new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    //テーブル作成
    $sql="CREATE TABLE IF NOT EXISTS mission5"
    ."("
    ."id INT AUTO_INCREMENT PRIMARY KEY,"//重複しないためprimary
    ."name char(32),"//名前３２字
    ."comment TEXT,"//コメントはテキスト
    ."date TEXT,"//date関数で取得してるからテキストで行けそう
    ."pass char(32)"//パス３２文字
    .");";
    $stmt = $pdo->query($sql);


    //フォームから受け取り
    $name = $_POST["name"];
    $comment = $_POST["str"];
    $pass=$_POST["pass"];
    $pass2=$_POST["pass2"];
    $pass3=$_POST["pass3"];
    $date=date("Y/m/d H:i:s");
    $delete=$_POST["delete"];
    $edit=$_POST["edit"];
    $edit2=$_POST["edit2"];

    //書き込み
    if (!empty($name && $comment && $pass && $edit2)) {//編集時の書き込み
        $id=$edit2;//editの番号を$idに設定
        $sql='UPDATE mission5 SET name=:name,comment=:comment,date=:date,pass=:pass WHERE id=:id';//setで変更したいカラム名
        $stmt=$pdo->prepare($sql);
        $stmt -> bindParam(':name', $name, PDO::PARAM_STR);//それぞれを入力された＄〜で再度書き込み
        $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
        $stmt -> bindParam(':pass', $pass, PDO::PARAM_STR);
        $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
        $stmt -> execute();//query実行
    } elseif (!empty($name && $comment &&$pass)) {//普通の書き込み
        $sql = $pdo -> prepare("INSERT INTO mission5 (name, comment, pass, date) VALUES (:name, :comment, :pass, :date)");
        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
        $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
        $sql -> execute();
    }



    //削除
    if (!empty($delete &&!empty($pass2))) {
        $id = $delete;
        $sql='select * from mission5 where id=:id';//mission5のid番目のもの全て
        $stmt = $pdo->prepare($sql);                  
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);//ここら辺無駄に多く描いてる気がするので見直す
        $stmt->execute(); 
        $results = $stmt->fetchAll();
        foreach ($results as $row) {
            if ($pass2==$row['pass']) {//パスが一致する時
                $sql = 'delete from mission5 where id=:id';//id番目を削除
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                echo "パスワードが違います";
            }
        }
    }

    //編集番号受け取り
    if (!empty($edit) &&!empty($pass3)) {
        $id=$edit;
        $sql='select * from mission5 where id=:id';//mission5のid番目のもの全て
        $stmt = $pdo->prepare($sql);  //ここもめんどい書き方してると思う
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();                         
        $results = $stmt->fetchAll();
        foreach ($results as $row) {
            if ($pass3==$row['pass']) {//パスと入力されたパス一致する時
                $editname= $row['name'];
                $editcomment= $row['comment'];
                $editnum= $row['id'];
            } else {
                echo "パスワードが違います";
            }
        }
    }
    
    //エラーメッセージ

    //普通の
    if(empty($name) && !empty($comment)){
        echo "名前を入力してください";
    }
    if(empty($comment) && !empty($name)){
        echo "コメントを入力してください";
    }
    if(empty($pass) && !empty($name && !empty($comment))){
        echo "パスワードを入力してください";
    }

    //削除
    if(empty($delete) && !empty($pass2)){
        echo "削除したい番号を入力してください";
    }
    if(!empty($delete) && empty($pass2)){
        echo "パスワードを入力してください";
    }

    //編集
    if(empty($edit) && !empty($pass3)){
        echo "編集したい番号を入力してください";
    }
    if(!empty($edit) && empty($pass3)){
        echo "パスワードを入力してください";
    }


?>

    
<form action="" method=post>
    <div class="contents">
    <input type="text" name="name" placeholder="名前" value="<?php if (isset($editname)) {
    echo $editname;
};?>">
    <input type="text" name="str" placeholder="コメント" value="<?php if (isset($editcomment)) {
    echo $editcomment;
};?>">
    <input type="hidden" name="edit2" value="<?php if (isset($editnum)) {
    echo $editnum;
};?>">
    <input type="text" name="pass" placeholder="パスワード">
    <input type="submit" value="送信">
    </div>

    <div class="contents">
    <input type="text" name="delete" placeholder="削除対象番号">
    <input type="text" name="pass2" placeholder="パスワード">
    <input type="submit" value="削除">
    </div>

    <div class="contents">
    <input type="text" name="edit" placeholder="編集番号">
    <input type="text" name="pass3" placeholder="パスワード">
    <input type="submit" value="編集">
    </div>
    </form>    




<?php
    //表示
        $sql = 'SELECT * FROM mission5';//mission5全部表示（パスはしないよ）
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row) {
            //$rowの中にはテーブルのカラム名が入る
            echo $row['id'].',';
            echo $row['name'].',';
            echo $row['comment'].',';
            echo $row['date'].'<br>';
            echo "<hr>";
        }


    
?>
    

</body>
</html>