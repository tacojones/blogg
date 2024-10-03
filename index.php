<?php
define('POSTS_PER_PAGE', 5);
require 'Parsedown.php';

$Parsedown = new Parsedown();

function get_posts(int $page, string $search_query = ''): array {
    $post_dir = 'posts';
    // Get all markdown files from the 'posts' directory
    $files = array_filter(scandir($post_dir), function($file) {
        return $file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'md';
    });

    // Sort files by modification time (newest first)
    usort($files, function($a, $b) use ($post_dir) {
        return filemtime("$post_dir/$b") - filemtime("$post_dir/$a");
    });

    // Paginate the sorted files
    $files = array_slice($files, ($page - 1) * POSTS_PER_PAGE, POSTS_PER_PAGE);

    $posts = [];
    foreach ($files as $file) {
        $post = parse_markdown("$post_dir/$file");
        // Filter posts by search query if provided
        if ($search_query === '' || 
            stripos($post['title'], $search_query) !== false || 
            stripos($post['content'], $search_query) !== false) {
            $post['filename'] = $file;
            $posts[] = $post;
        }
    }
    return $posts;
}

function get_total_pages(string $search_query = ''): int {
    $post_dir = 'posts';
    // Get all markdown files from the 'posts' directory
    $files = array_filter(scandir($post_dir), function($file) use ($post_dir) {
        return $file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'md';
    });

    // Filter and count valid posts based on search query
    $valid_files = array_filter($files, function($file) use ($post_dir, $search_query) {
        $post = parse_markdown("$post_dir/$file");
        return $search_query === '' || 
               stripos($post['title'], $search_query) !== false || 
               stripos($post['content'], $search_query) !== false;
    });

    // Calculate total pages based on the filtered posts
    $total_posts = count($valid_files);
    return ceil($total_posts / POSTS_PER_PAGE);
}

// Function to manually parse YAML front matter and content from .md files
function parse_markdown(string $filepath): array {
    if (!file_exists($filepath)) {
        return [
            'title' => 'Untitled',
            'date' => '',
            'content' => '',
        ];
    }

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
        'title' => htmlspecialchars($front_matter['title'] ?? 'Untitled'),
        'date' => htmlspecialchars($front_matter['date'] ?? ''),
        'content' => $content,
    ];
}

// Basic YAML parser for key-value pairs
function parse_yaml(string $yaml_string): array {
    $lines = explode("\n", $yaml_string);
    $data = [];

    foreach ($lines as $line) {
        if (strpos($line, ': ') !== false) {
            list($key, $value) = explode(': ', trim($line), 2);
            $data[trim($key)] = trim($value, '"\''); // Remove quotes if present
        }
    }

    return $data;
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$posts = get_posts($page, $search_query);
$total_pages = get_total_pages($search_query);
?>

<?php include 'includes/header.php'; ?>

<?php if (count($posts) > 0): ?>
    <?php foreach ($posts as $post): ?>
        <div class="post">
            <img class="avatar" src="images/avatar.png" alt="Avatar" />
            <h2><a href="view.php?file=<?= urlencode($post['filename']) ?>"><?= $post['title'] ?></a></h2>
            <div class="date"><?= $post['date'] ?></div>

            <?php
            // Check if there's a 'Read More' tag in the post content
            if (stripos($post['content'], '<!--more-->') !== false) {
                list($content, $rest) = explode('<!--more-->', $post['content'], 2);
                echo $Parsedown->text($content);
                echo '<p><a href="view.php?file=' . urlencode($post['filename']) . '">Read More</a></p>';
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
        <a href="index.php?page=<?= $page - 1 ?>&search=<?= urlencode($search_query) ?>">&laquo; Previous</a>
    <?php endif; ?>

    <?php if ($page < $total_pages): ?>
        <a href="index.php?page=<?= $page + 1 ?>&search=<?= urlencode($search_query) ?>">Next &raquo;</a>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
