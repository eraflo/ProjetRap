<!--Page où apparaissent les publications; Reste à améliorer le style et la mise en page mais opérationnel-->
<!--Connecte à base de données + gère affiche des articles-->
<?php
include 'tmpl_top.php'; 
$bdd = new PDO("mysql:host=127.0.0.1;dbname=articles;charset=utf8", "root", "");
$bdd2 = new PDO("mysql:host=127.0.0.1;dbname=espace_membre;charset=utf8", "root", "");
include('filtre.php');

if(isset($_GET['id']) AND !empty($_GET['id'])) {
    $get_id = htmlspecialchars($_GET['id']);
    $article = $bdd->prepare('SELECT * FROM articles WHERE id = ?');
    $article->execute(array($get_id));

    //Changer code BBCode en html
    $parser = new JBBCode\Parser();
    $parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
    $parser->addBBCode("quote", '<blockquote>{param}</blockquote>');

    if($article->rowCount() == 1) {
        $article = $article->fetch();
        $id = $article['id'];
        $titre = $article['titre'];
        $contenu = $article['contenu'];

        $likes = $bdd->prepare('SELECT id FROM likes WHERE id_article = ?');
        $likes->execute(array($id));
        $likes = $likes->rowCount();

        $dislikes = $bdd->prepare('SELECT id FROM dislikes WHERE id_article = ?');
        $dislikes->execute(array($id));
        $dislikes = $dislikes->rowCount();

    } else {
        die('Cette article n\'existe pas !!!');
    }

    if(!empty($_SESSION)) {
        if(isset($_POST['submit_commentaire'])) {
            if(isset($_POST['commentaire']) AND !empty($_POST['commentaire'])) {
                $pseudo = htmlspecialchars($_SESSION['pseudo']);
                $id_pseudo = intval($_SESSION['id']);
                $commentaire = htmlspecialchars($_POST['commentaire']);
                $ins = $bdd->prepare("INSERT INTO commentaires (pseudo, commentaire, id_article, id_pseudo) VALUES (?, ?, ?, ?)");
                $ins->execute(array($pseudo, $commentaire, $get_id, $id_pseudo));
                $msg = "Votre commentaire a été posté.";
                $lastcom = $commentaire;
                header("Location: Publication.php?id=".$get_id);
            } else {
                $msg = "Le champ est vide, remplissez le pour poster votre commentaire";
            }
        }
    } else {
        $msg = "Vous n'êtes pas membre, vous ne pouvez pas poster de commentaires, inscrivez-vous !!!";
    }

} else {
    die('Erreur');
}


$commentaires = $bdd->prepare("SELECT * FROM commentaires WHERE id_article = ? ORDER BY id DESC");
$commentaires->execute(array($get_id));
$emoji_replace = array(':I', ':o', ':I-', ']:}');
$emoji_new = array('<img src="assets/Inflow_logo_64px.png" />', '<img src="assets/Inflow_logo_64px.png" />', '<img src="assets/Inflow_logo_64px.png" />', '<img src="assets/Inflow_logo_64px.png" />');

?>

            <div class="left">
                <div class="navElement"><a href="Rap.php">Rap</a></div>
                <div class="navElement"><a href="MusiqueUrbaine.php">Musique Urbaine</a></div>
                <div class="navElement"><a href="ChroniquesJason.php">Les Chroniques de Jason</a></div>
            </div>

            <!--Début de là où on pourra mettre du texte-->
            <div class="middle">
                <article>

                    <!--Affiche les articles-->
                    <h1>
                        <?= $titre ?>
                    </h1>
                    <p>
                        <?php //affiche ici le contenu en html reçu de l'éditeur de texte
                        $parser->parse($contenu);
                        echo $parser->getAsHtml();
                        ?>
                    </p>
                    <a href="Action.php?t=1&id=<?= $id ?>">Like</a> (
                    <?= $likes ?>)
                        <a href="Action.php?t=2&id=<?= $id ?>">Dislike</a> (
                        <?= $dislikes ?>)

                </article>
            </div>

            <div class="right">
                <br/><br/>
                <div class="Commentaires">
                    <h2>Commentaires :</h2>
                    <form method="POST">
                        <textarea name="commentaire" placeholder="Votre commentaire"></textarea> <br/>
                        <input type="submit" value="Poster" name="submit_commentaire" />
                    </form>
                    <br/>
                    <?php if(isset($msg)) { echo $msg; } ?>
                    <br/>
                    <?php while($c = $commentaires->fetch()) {
                    $pseudoAvatar = $bdd2->prepare("SELECT * FROM membres WHERE id = ? ORDER BY id DESC");
                    $pseudoAvatar->execute(array($c['id_pseudo']));
                    $avatarInfos = $pseudoAvatar->fetch(); ?>
                    <?php if(!empty($avatarInfos)) { ?>
                    <img src="membres/avatars/<?php echo $avatarInfos['avatar']; ?>" width="50">
                    <?php } ?>
                    <b><?= $c['pseudo'] ?> :</b>
                    <?php $c['commentaire'] = str_replace($emoji_replace, $emoji_new, $c['commentaire']); ?>
                    <?php $c['commentaire'] = Filtre($c['commentaire']); ?>
                    <?= $c['commentaire'] ?> <br/>
                        <?php } ?>
                </div>
            </div>
<?php 
// Le bas de l'interface est ajouté après le contenu
include 'tmpl_bottom.php'; 
?>