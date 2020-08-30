<?php 
    // DB接続設定
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    //どのデータベースでも、同じ書き方で使用できるようにしたのがPDOです
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    //テーブル作成
    $sql = "CREATE TABLE IF NOT EXISTS message_board1"
    ." ("
 	. "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name VARCHAR(32),"
    . "comment TEXT,"
    . "date datetime"
    .");";
    $stmt = $pdo->query($sql);
    
    if(!empty($_POST["name"])&&!empty($_POST["comment"])) {
        $name = $_POST['name'];
	    $comment = $_POST['comment']; //好きな名前、好きな言葉は自分で決めること
	    $date = date("Y/m/d/H:i:d");
	    //$_POST['num_thrown']にするとarray()が教示され、更新できない
	    //empty($_POST["edit_num"])だと新規投稿になり分岐されない
        if(empty($_POST["num_thrown"])) {//新規登録
    
            $sql = $pdo -> prepare("INSERT INTO message_board1 (name, comment, date) VALUES (:name, :comment, :date)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);
    
            $sql -> execute();
            
        } else {//投稿編集$edit_num = $_POST['edit_num'];
            $editNum = $_POST["num_thrown"];
            //echo $editNum;
            
            $sql = "SELECT * FROM message_board1 WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt -> bindParam(':id', $editNum, PDO::PARAM_INT);
            $stmt -> execute();
            
            $row = $stmt->fetch();
            //echo $row['id'].',';;
            //echo $row['name'].',';
		    //echo $row['comment'].',';
		    //echo $row['date'].'<br>';
            //print_r($row);//ここまでは実行できている
            //if($row['id']==$editNum) {
            //if($editNum == $row['id']) {
            
                //echo 'updateのifです。';
            $sql = 'UPDATE message_board1 SET name=:name,comment=:comment,date=:date WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
            $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
            //var_dump($edit_num);
            $stmt -> bindParam(':id', $editNum, PDO::PARAM_INT);
            $stmt -> execute();
                
            //}
            
        }
    }
    
    //投稿削除
    if(!empty($_POST['delete'])) {
        $del_num = $_POST['del_num'];
        
        $stmt = $pdo->prepare('delete from message_board1 where id=:id');
        
        //$stmt -> bindParam('i', $del_num, PDO::PARAM_INT);
        $stmt->bindParam(':id', $del_num, PDO::PARAM_INT);
        $stmt->execute();
        
    }
    
    //投稿編集
    if(!empty($_POST['edit'])) {
        $edit_num = $_POST['edit_num'];
        
        $sql = "SELECT * FROM message_board1 WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt -> bindParam(':id', $edit_num, PDO::PARAM_INT);
        $stmt -> execute();
        //連想配列で取得？
        $rows = $stmt->fetch();
        //print_r($rows);
        if($rows['id']==$edit_num) {
            $editNum = $rows['id'];
            $editName = $rows['name'];
            $editComment = $rows['comment'];
            $editDate = $rows['date'];
        }
	    
        
        /*$sql = "SELECT * FROM message_board1";
        $stmt = $pdo->query($sql);
        //連想配列で取得？
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //print_r($rows);
        foreach($rows as $row) {
	        if($row['id']==$edit_num) {
	            $editNum = $row['id'];
                $editName = $row['name'];
                $editComment = $row['comment'];
                $editDate = $row['date'];
            }
	    }*/
    }
?>	
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>掲示板</title>
</head>
<body>
    <h1>掲示板</h1>
    <hr>
    <form method="POST" action="">
        名前：<input type="text" name="name" placeholder="名前" 
        value="<?php if(!empty($_POST['edit'])) {?>
        <?php echo htmlspecialchars($editName, ENT_QUOTES, 'UTF-8');?>
        <?php } ?>"><br>
        コメント：<input type="text" name="comment" placeholder="コメント"
        value="<?php if(!empty($_POST['edit'])) {?>
        <?php echo htmlspecialchars($editComment, ENT_QUOTES, 'UTF-8');?>
        <?php } ?>"><br>
        <input type="hidden" name="num_thrown" 
        value="<?php if(!empty($_POST['edit'])) {?>
        <?php echo htmlspecialchars($edit_num, ENT_QUOTES, 'UTF-8');?>
        <?php } ?>">
        <input type="submit" name="submit"><br>
    </form>
    <form method="POST" action="">
        削除番号：<input type="number" name="del_num" placeholder="削除番号"><br>
        <input type="submit" name="delete"><br>
    </form>
    <form method="POST" action="">
        編集番号：<input type="number" name="edit_num" placeholder="編集番号"><br>
        <input type="submit" name="edit"><br>
    </form>
    <?php 
        $sql = 'SELECT * FROM message_board1 ORDER BY id DESC';
	    $stmt = $pdo->query($sql); //$sqlの指示をqueryメソッドで命令する
	    $results = $stmt->fetchAll();
	    foreach($results as $row) {
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