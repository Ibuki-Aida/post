<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta lang="UTF-8">
        <title>mission_3-02</title>
    </head>
    <body>

        <?php
        
            # DB接続設定
            $dsn = 'データベース名';
            $user = 'ユーザー名';
            $password = 'パスワード';
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            
            # テーブル作成
            $sql = "CREATE TABLE IF NOT EXISTS tbboard"
            ." ("
            . "id INT AUTO_INCREMENT PRIMARY KEY,"
            . "name char(32),"
            . "comment TEXT,"
            . "date DATETIME,"
            . "password char(32)"
            .");";
            $stmt = $pdo->query($sql);    
            
            # 投稿機能
            if(!empty($_POST["name"])&&!empty($_POST["comment"])){

                # 書き込みに必要なデータ
                $name = $_POST["name"];
                $comment = $_POST["comment"];
                $date=new DateTime();
                $date=$date -> format('Y-m-d H:i:s');
                    
                # 通常書き込み
                if(empty($_POST["edit_num"] && !empty($_POST["pass"]))){
                    $password = $_POST["pass"];
                    
                    $sql = $pdo -> prepare("INSERT INTO tbboard (name, comment, date, password) 
                                            VALUES (:name, :comment, :date, :password)");
                    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                    $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                    $sql -> bindParam(':password', $password, PDO::PARAM_STR);
                    $sql -> execute();

                }else{
                    # 編集書き込み
                    $password = $_POST["pass"];
                    $id = $_POST["edit_num"];
                    $sql = 'SELECT * FROM tbboard';
                    $stmt = $pdo->query($sql);
                    $results = $stmt->fetchAll();

                    foreach($results as $row){
                        if($row['id'] == $id && $row['password'] == $password){
                            $sql = 'UPDATE tbboard SET name=:name, comment=:comment 
                                    WHERE id=:id';
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                            $stmt->execute();
                        }
                    }
                }
            }

            # 削除機能
            if(!empty($_POST["delete"]) && !empty($_POST["delete_pass"])){
                
                $delete_pass = $_POST["delete_pass"];

                $id = $_POST["delete"];
                $sql = 'SELECT * FROM tbboard';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                foreach($results as $row){
                    if($row['password'] == $delete_pass){
                        $sql = 'delete from tbboard where id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt->execute();
                    }
                }
            }

            # 編集選択
            if(!empty($_POST["edit"]) && !empty($_POST["edit_pass"])){

                $edit_pass = $_POST["edit_pass"];

                $id = $_POST["edit"];
                $sql = 'SELECT * FROM tbboard';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                foreach($results as $row){
                    if($id == $row['id'] && $row['password'] == $edit_pass){
                        $edit_num = $row['id'];
                        $edit_nam = $row['name'];
                        $edit_com = $row['comment'];
                    }
                }
            }
            
        ?>

        <form action="" method="post">
            <input type="text" name="name" placeholder="名前" value="<?php if(isset($edit_nam)){echo $edit_nam;} ?>"><br>
            <input type="text" name="comment" placeholder="コメント" value="<?php if(isset($edit_com)){echo $edit_com;} ?>"><br>
            <input type="text" name="pass" placeholder="パスワード">
            <input type="hidden" name="edit_num" placeholder="edit_num" value="<?php if(isset($edit_num)){echo $edit_num;} ?>">
            <input type="submit" name="submit1">
        </form>
        <br>
        
        <form action="" method="post">
            <input type="num" name="delete" placeholder="削除対象番号">
            <input type="text" name="delete_pass" placeholder="パスワード">
            <input type="submit" name="submit2" value="削除">
        </form>
        <br>

        <form action="" method="POST">
            <input type="num" name="edit" placeholder="編集対象番号">
            <input type="text" name="edit_pass" placeholder="パスワード">
            <input type="submit" name="submit3" value="編集">
        </form>
        <br>

        <?php

            # 画面に表示
	        $sql = 'SELECT * FROM tbboard';
	        $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
        
	        foreach ($results as $row){
		        echo $row['id'].',';
		        echo $row['name'].',';
		        echo $row['comment'].',';
		        echo $row['date'].'<br>';
		        echo "<hr>";
            }
        ?>

    </body>
</html>