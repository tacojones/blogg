<?php
require 'Parsedown.php';
$Parsedown = new Parsedown();
$file = $_GET['file'];
$post = json_decode(file_get_contents("posts/$file"), true);
?>

<?php
include 'includes/header.php';
?>
		<?php global $Parsedown; ?>
		<div class="post">
        <h2><?= htmlspecialchars($post['title']) ?></h2>
        <div class="date"><?= htmlspecialchars($post['date']) ?></div>
        <p><?= $Parsedown->text($post['content']) ?></p>
        </div>

<?php
include 'includes/footer.php';
?>
