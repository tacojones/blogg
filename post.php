<?php
// Include Parsedown and YAML parser
require_once __DIR__ . '/includes/Parsedown.php';
require_once __DIR__ . '/includes/YAMLParser.php';

$posts_dir = realpath(__DIR__ . '/posts/');
$pages_dir = realpath(__DIR__ . '/pages/');

$parsedown = new Parsedown();
$parsedown->setSafeMode(false);

// Helper function to ensure safe paths
function safeFilePath($base_dir, $requested_file) {
    // Use basename to prevent directory traversal attempts
    $requested_file = basename(str_replace(["\0", "../", "..\\"], '', $requested_file));
    $full_path = realpath($base_dir . '/' . $requested_file);

    // Ensure the file is still within the base directory
    if ($full_path !== false && strpos($full_path, $base_dir) === 0) {
        return $full_path;
    }

    return false;
}

// Helper function to recursively scan directories for .md files
function scanDirectoryForPosts($dir) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    $files = [];
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'md') {
            $files[] = $file->getPathname();
        }
    }
    return $files;
}

// Check if it's a static page or a post
if (isset($_GET['page'])) {
    // Handle static page from /pages
    $page = basename($_GET['page']); // Get the page file name safely

    // Sanitize and validate the path
    $pagePath = safeFilePath($pages_dir, $page);

    if ($pagePath && file_exists($pagePath)) {
        $content = file_get_contents($pagePath);
        if ($content === false) {
            echo 'Error loading page content.';
            exit;
        }
        $html_content = $parsedown->text($content);

        include __DIR__ . '/header.php';
        echo '<div class="post">';
        echo $html_content;
        echo '</div>';
        include __DIR__ . '/footer.php';
    } else {
        echo 'Page not found.';
    }
} elseif (isset($_GET['slug'])) {
    // Handle blog post from /posts
    $slug = $_GET['slug'];

    // Sanitize the slug to allow only alphanumeric characters and hyphens
    if (!preg_match('/^[a-zA-Z0-9-]+$/', $slug)) {
        echo 'Invalid post specified.';
        exit;
    }

    // Recursively get all Markdown files from the posts directory
    $files = scanDirectoryForPosts($posts_dir);

    $post_found = false;
    $md_content = '';
    $yaml = [];

    foreach ($files as $file_path) {
        // Ensure the file path is safe and within the posts directory
        $safe_file_path = safeFilePath($posts_dir, str_replace($posts_dir, '', $file_path));
        if ($safe_file_path === false) {
            continue;
        }

        $content = file_get_contents($safe_file_path);
        if ($content === false) {
            continue;
        }

        $content = str_replace("\r\n", "\n", $content); // Normalize line endings

        // Separate YAML front matter and Markdown content
        if (preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)/s', $content, $matches)) {
            $yaml_content = trim($matches[1]);
            $md_content = trim($matches[2]);
            $yaml = parseYAML($yaml_content);

            // Normalize keys to lowercase
            $yaml = array_change_key_case($yaml, CASE_LOWER);
        } else {
            $yaml = [];
            $md_content = $content;
        }

        // Ensure $yaml is an array
        if (!is_array($yaml)) {
            $yaml = [];
        }

        // Generate slug
        $post_slug = $yaml['slug'] ?? null;

        if (!$post_slug) {
            // Generate slug from the title or filename
            if (!empty($yaml['title'])) {
                $title_for_slug = $yaml['title'];
                // Remove any non-alphanumeric characters (except hyphens)
                $post_slug = preg_replace('/[^a-zA-Z0-9-]+/', '-', strtolower($title_for_slug));
                $post_slug = trim($post_slug, '-');
            } else {
                $post_slug = pathinfo($file_path, PATHINFO_FILENAME);
            }
        }

        // Compare with the requested slug
        if ($post_slug === $slug) {
            // Found the post
            $post_found = true;
            break;
        }
    }

    // If the post is not found, display an error
    if (!$post_found) {
        echo 'Post not found.';
        exit;
    }

    // Remove the <!--more--> tag from the content
    $md_content = str_replace('<!--more-->', '', $md_content);

    // Parse the Markdown content
    $html_content = $parsedown->text($md_content);

    include __DIR__ . '/header.php';
    ?>
    <div class="post">
    <div class="avatar"></div>
        <?php
        // Fetch and decode the title
        $title = $yaml['title'] ?? 'Untitled';
        $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
        $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        ?>
        <h3><?php echo $title; ?></h3>

        <?php
        if (!empty($yaml['date'])) {
            $dateStr = $yaml['date'];

            // Try parsing the date using multiple formats
            $dateFormats = [
                'Y-m-d H:i:s',
                'Y-m-d H:i',
                'Y-m-d',
                'Y-m-d\TH:i:sP', // ISO 8601 format
                'Y-m-d\TH:i:s',  // ISO 8601 without timezone
                'D, d M Y H:i:s O', // RFC 2822
            ];

            $date = null;
            foreach ($dateFormats as $format) {
                $date = DateTime::createFromFormat($format, $dateStr);
                $errors = DateTime::getLastErrors();
                if ($date && $errors && $errors['warning_count'] == 0 && $errors['error_count'] == 0) {
                    break;
                }
            }

            if (!$date) {
                // Fallback using strtotime()
                $timestamp = strtotime($dateStr);
                if ($timestamp !== false) {
                    $date = (new DateTime())->setTimestamp($timestamp);
                }
            }

            if ($date) {
                $formatted_date = htmlspecialchars($date->format('F j, Y, g:i A'), ENT_QUOTES, 'UTF-8');
            } else {
                $formatted_date = htmlspecialchars($yaml['date'], ENT_QUOTES, 'UTF-8');
            }

            echo '<p class="date">' . $formatted_date . '</p>';
        }
        ?>

        <?php echo $html_content; ?>
    </div>
    <?php
    include __DIR__ . '/footer.php';
} else {
    echo 'No page or post specified.';
    exit;
}
?>
