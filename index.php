<?php
define('POSTS_PER_PAGE', 10);
require 'Parsedown.php';
$Parsedown = new Parsedown();

function get_posts($page, $search_query = '') {
    $files = array_slice(scandir('posts', SCANDIR_SORT_DESCENDING), ($page - 1) * POSTS_PER_PAGE, POSTS_PER_PAGE);
    $posts = [];
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $post = json_decode(file_get_contents("posts/$file"), true);
            if ($search_query === '' || stripos($post['title'], $search_query) !== false || stripos($post['content'], $search_query) !== false) {
                $posts[] = $post;
            }
        }
    }
    return $posts;
}

function get_total_pages($search_query = '') {
    $files = array_filter(scandir('posts'), function($file) use ($search_query) {
        if ($file == '.' || $file == '..') {
            return false;
        }
        $post = json_decode(file_get_contents("posts/$file"), true);
        return $search_query === '' || stripos($post['title'], $search_query) !== false || stripos($post['content'], $search_query) !== false;
    });
    $total_posts = count($files);
    return ceil($total_posts / POSTS_PER_PAGE);
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$posts = get_posts($page, $search_query);
$total_pages = get_total_pages($search_query);
?>

<?php
include 'includes/header.php';
?>
   
        <?php if (count($posts) > 0): ?>
         <?php global $Parsedown; ?>
            <?php foreach ($posts as $post): ?>
                <div class="post">
                    <img class="avatar" src="avatar.png" />
                    <h2><a href="view.php?file=<?= htmlspecialchars($post['filename']) ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
                    <div class="date"><?= htmlspecialchars($post['date']) ?></div>

        <?php if (stripos($post['content'], '<!--more-->') !== false) {
            list($content, $rest) = explode('<!--more-->', $post['content'], 2);
            $content = $Parsedown->text($content);
            echo $content;
            echo '<p><a href="view.php?file=' .htmlspecialchars($post['filename']). '">Read More</a></p>';
        } else {
            // No <!--more--> tag, display full content
            $content = $Parsedown->text($post['content']);
            echo $content;
        } ?>
                
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center;" class="post">
            <h2>Nothing Found</h2>
            </div>
        <?php endif; ?>

        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="index.php?page=<?= $page - 1 ?>&search=<?= htmlspecialchars($search_query) ?>">&laquo; Previous</a>
            <?php endif; ?>

            <?php if ($page < $total_pages): ?>
                <a href="index.php?page=<?= $page + 1 ?>&search=<?= htmlspecialchars($search_query) ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>
        
<?php
include 'includes/footer.php';
?>
