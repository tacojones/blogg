<?php
// Directory containing markdown files
$directory = 'posts';

// Function to manually parse YAML front matter
function parse_markdown($filepath) {
    $file_contents = file_get_contents($filepath);

    // Split content into front matter and markdown content
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

// Read markdown files and parse their front matter
function get_posts($directory) {
    $posts = [];
    $files = scandir($directory, SCANDIR_SORT_DESCENDING);

    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'md') {
            $file_path = "$directory/$file";
            $post = parse_markdown($file_path);
            if (!empty($post['date'])) {
                $post['filename'] = $file;
                $post['filemtime'] = filemtime($file_path); // Get file modification time
                $posts[] = $post;
            }
        }
    }

    // Sort posts by file modification time in descending order
    usort($posts, function ($a, $b) {
        return $b['filemtime'] - $a['filemtime'];
    });

    return $posts;
}

// Group posts by month
function group_posts_by_month($posts) {
    $grouped_posts = [];

    foreach ($posts as $post) {
        $month = date('F Y', $post['filemtime']);
        $grouped_posts[$month][] = $post;
    }

    return $grouped_posts;
}

// Get and group posts
$posts = get_posts($directory);
$grouped_posts = group_posts_by_month($posts);
?>

<?php
include 'includes/header.php';
?>
<div class="post">
    <h2>Blog Archive</h2>
    <div class="search">
        <form method="GET" action="index.php">
            <input type="text" name="search" placeholder="" class="searchinput" value="">
            <button type="submit">Search</button>
        </form>
    </div>
    <?php if (!empty($grouped_posts)): ?>
        <?php foreach ($grouped_posts as $month => $posts): ?>
            <h3><?= htmlspecialchars($month) ?></h3>
            <ul>
                <?php foreach ($posts as $post): ?>
                    <li><a href="view.php?file=<?= urlencode($post['filename']) ?>"><?= htmlspecialchars($post['title']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No posts available.</p>
    <?php endif; ?>
</div>
<?php
include 'includes/footer.php';
?>

