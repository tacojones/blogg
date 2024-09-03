<?php
define('POSTS_PER_PAGE', 5);
require 'Parsedown.php';
$Parsedown = new Parsedown();

// Function to read and parse posts from .md files
function get_posts($page, $search_query = '') {
    $files = array_slice(scandir('posts', SCANDIR_SORT_DESCENDING), ($page - 1) * POSTS_PER_PAGE, POSTS_PER_PAGE);
    $posts = [];
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && pathinfo($file, PATHINFO_EXTENSION) === 'md') {
            $post = parse_markdown_file("posts/$file");
            if ($search_query === '' || stripos($post['title'], $search_query) !== false || stripos($post['content'], $search_query) !== false) {
                $post['filename'] = $file; // Add filename to post for linking
                $posts[] = $post;
            }
        }
    }
    return $posts;
}

// Function to calculate total pages based on the number of .md files
function get_total_pages($search_query = '') {
    $files = array_filter(scandir('posts'), function($file) use ($search_query) {
        if ($file == '.' || $file == '..' || pathinfo($file, PATHINFO_EXTENSION) !== 'md') {
            return false;
        }
        $post = parse_markdown_file("posts/$file");
        return $search_query === '' || stripos($post['title'], $search_query) !== false || stripos($post['content'], $search_query) !== false;
    });
    $total_posts = count($files);
    return ceil($total_posts / POSTS_PER_PAGE);
}

// Custom function to parse YAML front matter manually
function parse_yaml_front_matter($content) {
    $metadata = [];
    if (preg_match('/^---\s*(.*?)\s*---/s', $content, $matches)) {
        $lines = explode("\n", trim($matches[1]));
        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                list($key, $value) = explode(':', $line, 2);
                $metadata[trim($key)] = trim($value);
            }
        }
        // Remove front matter from content
        $content = str_replace($matches[0], '', $content);
    }
    return [$metadata, $content];
}

// Function to parse the content of .md files
function parse_markdown_file($file_path) {
    $content = file_get_contents($file_path);
    
    // Extract metadata from front matter
    list($metadata, $content) = parse_yaml_front_matter($content);
    
    // Set default title and date if not present in front matter
    $title = $metadata['title'] ?? 'Untitled Post';
    $date = $metadata['date'] ?? date('Y-m-d');
    
    return [
        'title' => $title,
        'date' => $date,
        'content' => $content
    ];
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$posts = get_posts($page, $search_query);
$total_pages = get_total_pages($search_query);

include 'includes/header.php';
?>

<?php if (count($posts) > 0): ?>
    <?php global $Parsedown; ?>
    <?php foreach ($posts as $post): ?>
        <div class="post">
            <img class="avatar" src="avatar.png" />
            <h2><a href="view.php?file=<?= htmlspecialchars($post['filename']) ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
            <div class="date"><?= htmlspecialchars($post['date']) ?></div>

            <?php
            // Check for <!--more--> tag to split content
            if (stripos($post['content'], '<!--more-->') !== false) {
                list($content, $rest) = explode('<!--more-->', $post['content'], 2);
                $content = $Parsedown->text($content);
                echo $content;
                echo '<p><a href="view.php?file=' .htmlspecialchars($post['filename']). '">Read More</a></p>';
            } else {
                // No <!--more--> tag, display full content
                $content = $Parsedown->text($post['content']);
                echo $content;
            }
            ?>
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
