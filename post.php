<?php
// Include Parsedown and YAML parser
require_once __DIR__ . '/includes/Parsedown.php';
require_once __DIR__ . '/includes/YAMLParser.php';

$posts_dir = realpath(__DIR__ . '/posts/');
$pages_dir = realpath(__DIR__ . '/pages/');
$parsedown = new Parsedown();
$parsedown->setSafeMode(false);

// Helper function to ensure safe file paths
function safeFilePath($base_dir, $requested_file) {
    $requested_file = str_replace(["\0", "../", "..\\"], '', $requested_file);
    $full_path = realpath($base_dir . '/' . $requested_file);
    return ($full_path && strpos($full_path, $base_dir) === 0) ? $full_path : false;
}

// Helper function to generate a slug from a title
function generateSlug($title) {
    return trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($title)), '-');
}

// Check if a static page or a post is requested
if (isset($_GET['page'])) {
    // Handle static page from /pages
    $page = basename($_GET['page']);
    $pagePath = safeFilePath($pages_dir, $page);

    if ($pagePath && file_exists($pagePath)) {
        $content = file_get_contents($pagePath);
        echo $parsedown->text($content);
    } else {
        echo 'Page not found.';
    }
} elseif (isset($_GET['slug'])) {
    // Handle blog post from /posts
    $slug = $_GET['slug'];
    $found_post = false;

    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($posts_dir)) as $file) {
        if ($file->isFile() && $file->getExtension() === 'md') {
            $content = file_get_contents($file->getPathname());

            if (preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)/s', $content, $matches)) {
                $yaml = parseYAML(trim($matches[1]));
                $md_content = trim($matches[2]);

                // Generate a slug based on the title or from YAML if available
                $post_slug = $yaml['slug'] ?? generateSlug($yaml['title'] ?? pathinfo($file->getFilename(), PATHINFO_FILENAME));

                // If slugs match, display the post
                if ($post_slug === $slug) {
                    $found_post = true;
                    include __DIR__ . '/header.php';
                    echo '<div class="post">';
                    
                    // Add avatar div here
                    echo '<div class="avatar"></div>';

                    echo '<h3>' . htmlspecialchars($yaml['title'] ?? 'Untitled') . '</h3>';
                    
                    // Display formatted date if available
                    if (!empty($yaml['date'])) {
                        $date = DateTime::createFromFormat('Y-m-d H:i:s', $yaml['date']) ?: DateTime::createFromFormat('Y-m-d', $yaml['date']);
                        echo '<p class="date">' . htmlspecialchars($date ? $date->format('F j, Y, g:i A') : $yaml['date']) . '</p>';
                    }

                    echo $parsedown->text(str_replace('<!--more-->', '', $md_content));
                    echo '</div>';
                    include __DIR__ . '/footer.php';
                    break;
                }
            }
        }
    }

    if (!$found_post) {
        echo 'Post not found.';
    }
} else {
    echo 'No page or post specified.';
}
?>

