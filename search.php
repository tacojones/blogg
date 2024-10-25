<?php
// Include Parsedown and YAML parser
require_once __DIR__ . '/includes/Parsedown.php';
require_once __DIR__ . '/includes/YAMLParser.php';

$posts_dir = realpath(__DIR__ . '/posts/');
$parsedown = new Parsedown();
$parsedown->setSafeMode(false);

// Helper function to safely read file contents
function safeFilePath($base_dir, $requested_file) {
    $requested_file = str_replace(["\0", "../", "..\\"], '', $requested_file);
    $full_path = realpath($base_dir . '/' . $requested_file);
    return ($full_path !== false && strpos($full_path, $base_dir) === 0) ? $full_path : false;
}

// Sanitize slugs to ensure they are URL-friendly
function generateSlug($string) {
    // Convert the string to lowercase
    $slug = strtolower($string);
    
    // Remove all characters that are not alphanumeric or hyphens
    $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
    
    // Trim any hyphens from the start and end of the slug
    $slug = trim($slug, '-');
    
    return $slug;
}

// Get the search query
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($query === '') {
    echo 'Please enter a search query.';
    exit;
}

// Escape special characters for safe search
$search_query = htmlspecialchars($query, ENT_QUOTES, 'UTF-8');

// Get all Markdown files from the posts directory
$files = glob($posts_dir . '/*.md');
$results = [];

foreach ($files as $file_path) {
    $safe_file_path = safeFilePath($posts_dir, basename($file_path));
    if ($safe_file_path === false) {
        continue;
    }

    $content = file_get_contents($safe_file_path);
    if ($content === false) {
        continue;
    }

    // Normalize line endings
    $content = str_replace("\r\n", "\n", $content);

    // Separate YAML front matter and Markdown content
    if (preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)/s', $content, $matches)) {
        $yaml_content = trim($matches[1]);
        $md_content = trim($matches[2]);
        $yaml = parseYAML($yaml_content);
    } else {
        $yaml = [];
        $md_content = $content;
    }

    // Normalize keys to lowercase
    $yaml = array_change_key_case($yaml, CASE_LOWER);

    // Get the title for the search
    $title = isset($yaml['title']) ? $yaml['title'] : 'Untitled';

    // Check if the search query matches the title or content
    if (stripos($title, $search_query) !== false || stripos($md_content, $search_query) !== false) {
        // Generate slug from title
        $post_slug = isset($yaml['title']) ? generateSlug($yaml['title']) : generateSlug(pathinfo($file_path, PATHINFO_FILENAME));

        // Add result to the results array
        $results[] = [
            'title' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
            'slug' => $post_slug,
            'date' => $yaml['date'] ?? null,
        ];
    }
}

// Display search results
include __DIR__ . '/header.php';
?>

<h1>Search Results for "<?php echo $search_query; ?>"</h1>

<?php if (count($results) > 0): ?>
    <ul>
    <?php foreach ($results as $post): ?>
        <li>
            <a href="post.php?slug=<?php echo urlencode($post['slug']); ?>">
                <?php echo $post['title']; ?>
            </a>
            <?php if ($post['date']): ?>
                <span>(<?php echo htmlspecialchars($post['date'], ENT_QUOTES, 'UTF-8'); ?>)</span>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No results found for "<?php echo $search_query; ?>".</p>
<?php endif; ?>

<?php
include __DIR__ . '/footer.php';
?>

