<?php
session_start();
$bdd = new PDO("mysql:host=127.0.0.1;dbname=articles;charset=utf8", "root", "");
$bdd2 = new PDO("mysql:host=127.0.0.1;dbname=espace_membre;charset=utf8", "root", "");

if(isset($_GET['t'], $_GET['id'], $_SESSION['id'], $_SESSION) AND !empty($_GET['t']) AND !empty($_GET['id']) AND !empty($_SESSION['id'])) {
    $getid = (int) $_GET['id'];
    $gett = (int) $_GET['t'];

    $sessionid = $_SESSION['id'];

    $check = $bdd->prepare('SELECT id FROM articles WHERE id = ?');
    $check->execute(array($getid));

    if($check->rowCount() == 1) {
        if($gett == 1) {
            $check_like = $bdd->prepare('SELECT id FROM likes WHERE id_article = ? AND id_membre = ?');
            $check_like->execute(array($getid, $sessionid));

            $del = $bdd->prepare('DELETE FROM dislikes WHERE id_article = ? AND id_membre = ?');
            $del->execute(array($getid, $sessionid));

            if($check_like->rowCount() == 1) {
                $del = $bdd->prepare('DELETE FROM likes WHERE id_article = ? AND id_membre = ?');
                $del->execute(array($getid, $sessionid));
            } else {
                $ins = $bdd->prepare('INSERT INTO likes (id_article, id_membre) VALUES (?, ?)');
                $ins->execute(array($getid, $sessionid));
            }
        } elseif($gett == 2) {
            $check_dislike = $bdd->prepare('SELECT id FROM dislikes WHERE id_article = ? AND id_membre = ?');
            $check_dislike->execute(array($getid, $sessionid));

            $del = $bdd->prepare('DELETE FROM likes WHERE id_article = ? AND id_membre = ?');
            $del->execute(array($getid, $sessionid));

            if($check_dislike->rowCount() == 1) {
                $del = $bdd->prepare('DELETE FROM dislikes WHERE id_article = ? AND id_membre = ?');
                $del->execute(array($getid, $sessionid));
            } else {
                $ins = $bdd->prepare('INSERT INTO dislikes (id_article, id_membre) VALUES (?, ?)');
                $ins->execute(array($getid, $sessionid));
            }
        }
        header('Location: Publication.php?id='.$getid);
    } else {
        header('Location: main.php');
    }
} else {
    header('Location: main.php');
}

?>