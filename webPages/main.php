<?php
session_start();
$bdd = new PDO("mysql:host=127.0.0.1;dbname=inflow;charset=utf8", "root", "");
// Le haut de l'interface est ajouté avant le contenu
include 'tmpl_top.php';
?>
            <?php
            include 'LEFT/begin.php';
            include 'LEFT/categories.php';
            include 'LEFT/end.php';
            ?>
<!--Début de là où on pourra mettre du texte-->
<div class="middle">
    <article>
        <a href="Profil.php?id=6">Jason</a>
        <a href="Profil.php?id=2">Anyr</a>
        <a href="Profil.php?id=4">Axel</a>
    </article>
</div>
<div class="right"></div>
<?php 
// Le bas de l'interface est ajouté après le contenu
include 'tmpl_bottom.php'; 
?>