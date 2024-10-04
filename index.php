<?php
const POSTS_PER_PAGE = 5;
require 'Parsedown.php';

$Parsedown = new Parsedown();
$Parsedown->setSafeMode(true); // Ensure safe parsing to prevent XSS

// Define your desired date formats
const FILE_DATE_FORMAT = 'Y-m-d'; // Format for file names
const DISPLAY_DATE_FORMAT = 'F j, Y'; // Format for displaying post dates

function get_filtered_posts(string $search_query = ''): array {
    $post_dir = 'posts';
    $posts = [];

    // Get all markdown files from the 'posts' directory
    $files = glob("$post_dir/*.md");

    foreach ($files as $filepath) {
        $post = parse_markdown($filepath);

        // Filter posts by search query if provided
        if ($search_query === '' ||
            stripos($post['title'], $search_query) !== false ||
            stripos($post['content'], $search_query) !== false) {
            $post['filename'] = basename($filepath);
            $posts[] = $post;
        }
    }

    return $posts;
}

function get_posts(int $page, string $search_query = ''): array {
    $posts = get_filtered_posts($search_query);

    // Sort posts by date (newest first)
    usort($posts, function($a, $b) {
        $a_time = $a['timestamp'] ?? 0;
        $b_time = $b['timestamp'] ?? 0;
        return $b_time - $a_time;
    });

    // Paginate the sorted posts
    return array_slice($posts, ($page - 1) * POSTS_PER_PAGE, POSTS_PER_PAGE);
}

function get_total_pages(string $search_query = ''): int {
    $posts = get_filtered_posts($search_query);
    return ceil(count($posts) / POSTS_PER_PAGE);
}

// Function to parse YAML front matter and content from .md files
function parse_markdown(string $filepath): array {
    if (!file_exists($filepath)) {
        return [
            'title' => 'Untitled',
            'date' => '',
            'timestamp' => 0,
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

    // Parse the date from front matter
    $date_str = $front_matter['date'] ?? '';
    $timestamp = strtotime($date_str);
    $formatted_date = $timestamp ? date(DISPLAY_DATE_FORMAT, $timestamp) : '';

    return [
        'title' => htmlspecialchars($front_matter['title'] ?? 'Untitled'),
        'date' => $formatted_date,
        'timestamp' => $timestamp ?: filemtime($filepath),
        'content' => $content,
    ];
}

// Improved YAML parser
function parse_yaml(string $yaml_string): array {
    $lines = explode("\n", $yaml_string);
    $data = [];

    foreach ($lines as $line) {
        if (preg_match('/^\s*([^\s:]+)\s*:\s*(.*?)\s*$/', $line, $matches)) {
            $key = trim($matches[1]);
            $value = trim($matches[2], '"\''); // Remove quotes if present
            $data[$key] = $value;
        }
    }

    return $data;
}

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$posts = get_posts($page, $search_query);
$total_pages = get_total_pages($search_query);
?>

<?php include 'includes/header.php'; ?>

<?php if (count($posts) > 0): ?>
    <?php foreach ($posts as $post): ?>
        <div class="post">
            <img class="avatar" src="images/avatar.png" alt="Avatar" />
            <h2><a href="view.php?file=<?= urlencode($post['filename']) ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
            <div class="date"><?= htmlspecialchars($post['date']) ?></div>

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
        <a href="index.php?page=<?= $page - 1 ?>&amp;search=<?= urlencode($search_query) ?>">&laquo; Previous</a>
    <?php endif; ?>

    <?php if ($page < $total_pages): ?>
        <a href="index.php?page=<?= $page + 1 ?>&amp;search=<?= urlencode($search_query) ?>">Next &raquo;</a>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
