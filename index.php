<?php
define('POSTS_PER_PAGE', 5);
require 'Parsedown.php';
$Parsedown = new Parsedown();

function get_posts($page, $search_query = '') {
    // Get all markdown files from the 'posts' directory
    $files = array_filter(scandir('posts'), function($file) {
        return $file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'md';
    });

    // Sort files by modification time (newest first)
    usort($files, function($a, $b) {
        return filemtime("posts/$b") - filemtime("posts/$a");
    });

    // Paginate the sorted files
    $files = array_slice($files, ($page - 1) * POSTS_PER_PAGE, POSTS_PER_PAGE);

    $posts = [];
    foreach ($files as $file) {
        $post = parse_markdown("posts/$file");
        // Filter posts by search query if provided
        if ($search_query === '' || stripos($post['title'], $search_query) !== false || stripos($post['content'], $search_query) !== false) {
            $post['filename'] = $file;
            $posts[] = $post;
        }
    }
    return $posts;
}

function get_total_pages($search_query = '') {
    // Get all markdown files from the 'posts' directory
    $files = array_filter(scandir('posts'), function($file) use ($search_query) {
        if ($file === '.' || $file === '..' || pathinfo($file, PATHINFO_EXTENSION) !== 'md') {
            return false;
        }
        $post = parse_markdown("posts/$file");
        return $search_query === '' || stripos($post['title'], $search_query) !== false || stripos($post['content'], $search_query) !== false;
    });

    // Calculate total pages based on the filtered posts
    $total_posts = count($files);
    return ceil($total_posts / POSTS_PER_PAGE);
}

// Function to manually parse YAML front matter and content from .md files
function parse_markdown($filepath) {
    $file_contents = file_get_contents($filepath);

    // Split content into front matter and markdown content using regex
    if (preg_match('/---\s*(.*?)\s*---\s*(.*)/s', $file_contents, $matches)) {
        $front_matter = parse_yaml($matches[1]);
        $content = trim($matches[2]);
    } else {
        $front_matter = [];
        $content = trim($file_contents);
    }

    return [
        'title' => $front_matter['title'] ?? 'Untitled',
        'date' => $front_matter['date'] ?? '',
        'content' => $content,
    ];
}

// Basic YAML parser for key-value pairs
function parse_yaml($yaml_string) {
    $lines = explode("\n", $yaml_string);
    $data = [];

    foreach ($lines as $line) {
        if (strpos($line, ': ') !== false) {
            list($key, $value) = explode(': ', trim($line), 2);
            $data[$key] = trim($value, '"\''); // Remove quotes if present
        }
    }

    return $data;
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$posts = get_posts($page, $search_query);
$total_pages = get_total_pages($search_query);
?>

<?php include 'includes/header.php'; ?>

<?php if (count($posts) > 0): ?>
    <?php global $Parsedown; ?>
    <?php foreach ($posts as $post): ?>
        <div class="post">
            <img class="avatar" src="avatar.png" />
            <h2><a href="view.php?file=<?= htmlspecialchars($post['filename']) ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
            <div class="date"><?= htmlspecialchars($post['date']) ?></div>

            <?php
            // Check if there's a 'Read More' tag in the post content
            if (stripos($post['content'], '<!--more-->') !== false) {
                list($content, $rest) = explode('<!--more-->', $post['content'], 2);
                echo $Parsedown->text($content);
                echo '<p><a href="view.php?file=' . htmlspecialchars($post['filename']) . '">Read More</a></p>';
            } else {
                // Display full content if no 'Read More' tag is found
                echo $Parsedown->text($post['content']);
            }
            ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div style="text-align: center; margin: 100px; border: 0;" class="post">
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

<?php include 'includes/footer.php'; ?>

