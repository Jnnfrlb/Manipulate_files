<?php
if (isset($_POST["contenu"])) {
    $fichier = $_POST['file'];
    $fileToModify = fopen($fichier, "w");
    fwrite($fileToModify, $_POST["contenu"]);
    fclose($fileToModify);
}

if (isset($_POST['delete'])) {
    if (is_dir($_POST['file'])) {
        deleteDirectory($_POST['file']);
    } else {
        unlink($_POST['file']);
    }
    header('Location: index.php');
}
include('inc/head.php'); ?>

    <body>
    <div class="container-fluid">
        <h1>Files List</h1>

        <?php

        // Parcourt un dossier -> si c'est un dossier -> ouvre le dossier -> récupère la liste des fichiers -> affiche les fichiers
        function browseDirectory($path, $level)
        {
            if (is_dir($path)) {
                if ($handle = opendir($path)) {
                    $dirFiles = array();
                    while (false !== ($entry = readdir($handle))) {
                        $dirFiles[] = $entry;
                    }
                    // affiche liste fichiers
                    foreach ($dirFiles as $entry) {
                        if ($entry != "." && $entry != "..") {
                            if (is_dir($path . '/' . $entry)) {
                                // affiche le nom du dossier
                                echo '<h3 style="color: green;">' . $entry . '</h3>';
                                // affiche le contenu du dossier
                                echo '<div style="margin-left:30px; margin-top:4px;">';
                                browseDirectory($path . '/' . $entry, $level + 1);
                                echo '</div>';
                            } else {
                                echo '<a href="?file=' . $path . '/' . $entry . ' " >' . $entry . '</a><br/>';
                            }
                        }
                    }
                    closedir($handle);
                }
            } else {
                if ($path != "." && $path != "..") {
                    echo '<a href="' . $path . '">' . $path . '</a><br/>';
                }
            }
        }

        browseDirectory("./files", 0);

        // Fonction supprimer :
        function deleteDirectory($path)
        {
            if (is_dir($path))
                $handle = opendir($path);
            if (!$handle) {
                return false;
            }
            while ($entry = readdir($handle)) {
                if ($entry != '.' && $entry != '..') {
                    if (!is_dir($path . '/' . $entry)) {
                        unlink($path . '/' . $entry);
                    } else {
                        deleteDirectory($path . '/' . $entry);
                    }
                }
            }
            closedir($handle);
            rmdir($path);
            return true;
        }

        // Formulaire :
        if (isset($_GET['file'])) {
            echo $_GET['file'];
            $link = $_GET['file'];
            $contenu = file_get_contents($link);
            ?>

            <form method="POST" action="index.php">
            <div style="text-align: center;">
                <textarea name="contenu" style="width:90%; height:200px;">
                <?php echo $contenu; ?>
            </textarea>
                <input type="hidden" name="file" value="<?php echo $_GET['file'] ?>"/>
                <br>
                <input type="submit" value="Modify"/>
                <input type="submit" name="delete" value="Delete"/>
            </div>

            </form>

        <?php }
        ?>

    </div>
    </body>

<?php include('inc/foot.php'); ?>