<?php
// Include Parsedown and YAML parser
require_once __DIR__ . '/includes/Parsedown.php';
require_once __DIR__ . '/includes/YAMLParser.php';

$posts_dir = __DIR__ . '/posts/';
$posts_per_page = 5;

// Function to recursively get all Markdown files from the posts directory
function getMarkdownFiles($dir) {
    $files = [];
    foreach (glob($dir . '/*') as $file) {
        if (is_dir($file)) {
            $files = array_merge($files, getMarkdownFiles($file));
        } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'md') {
            $files[] = $file;
        }
    }
    return $files;
}

// Get all Markdown files from the posts directory
$all_files = getMarkdownFiles($posts_dir);

function getPostDate($file)
{
    $content = file_get_contents($file);
    if ($content === false) {
        return null;
    }
    $content = str_replace("\r\n", "\n", $content); // Normalize line endings

    // Extract YAML front matter
    if (preg_match('/^---[\s\n]*(.*?)\n---/s', $content, $matches)) {
        $yaml_content = trim($matches[1]);
        $yaml = parseYAML($yaml_content);

        // Normalize keys to lowercase
        if (is_array($yaml)) {
            $yaml = array_change_key_case($yaml, CASE_LOWER);
        }

        if (is_array($yaml) && isset($yaml['date'])) {
            $dateStr = $yaml['date'];
            // Try parsing the date with time if available
            $dateFormats = [
                'Y-m-d\TH:i:sP', // ISO 8601 format
                'Y-m-d\TH:i:s',  // ISO 8601 without timezone
                'Y-m-d H:i:s',
                'Y-m-d H:i',
                'Y-m-d',
                'D, d M Y H:i:s O', // RFC 2822
            ];
            foreach ($dateFormats as $format) {
                $date = DateTime::createFromFormat($format, $dateStr);
                $errors = DateTime::getLastErrors();
                if ($date && $errors && $errors['warning_count'] == 0 && $errors['error_count'] == 0) {
                    return $date; // Return valid DateTime
                }
            }
            // Fallback to using strtotime
            $timestamp = strtotime($dateStr);
            if ($timestamp !== false) {
                return (new DateTime())->setTimestamp($timestamp);
            }
        }
    }

    // Use file modification time as fallback
    $mtime = filemtime($file);
    if ($mtime !== false) {
        return (new DateTime())->setTimestamp($mtime);
    }
    return null;
}

usort($all_files, function ($a, $b) {
    $dateA = getPostDate($a);
    $dateB = getPostDate($b);

    if ($dateA === null && $dateB === null) {
        return 0; // Treat as equal
    }
    if ($dateA === null) {
        return 1; // $b is newer
    }
    if ($dateB === null) {
        return -1; // $a is newer
    }

    return $dateB->getTimestamp() <=> $dateA->getTimestamp(); // Descending order
});

// Pagination
$total_posts = count($all_files);
$total_pages = max(ceil($total_posts / $posts_per_page), 1);

// Get current page from query parameter with validation
$current_page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, [
    'options' => [
        'default'   => 1,
        'min_range' => 1,
        'max_range' => $total_pages,
    ],
]);

// Calculate the offset
$offset = ($current_page - 1) * $posts_per_page;

// Slice the array of files for the current page
$files = array_slice($all_files, $offset, $posts_per_page);

// Instantiate Parsedown and disable safe mode to allow raw HTML
$parsedown = new Parsedown();
$parsedown->setSafeMode(false); // Make sure raw HTML is properly sanitized elsewhere

// Include the header
include __DIR__ . '/header.php';

// Display posts
foreach ($files as $file) {
    $content = @file_get_contents($file);
    if ($content === false) {
        continue;
    }
    $content = str_replace("\r\n", "\n", $content); // Normalize line endings

    // Separate YAML front matter and Markdown content
    if (preg_match('/^---[\s\n]*(.*?)\n---[\s\n]*(.*)/s', $content, $matches)) {
        $yaml_content = trim($matches[1]);
        $yaml = parseYAML($yaml_content);
        $md_content = trim($matches[2]);

        // Normalize YAML keys to lowercase
        if (is_array($yaml)) {
            $yaml = array_change_key_case($yaml, CASE_LOWER);
        }
    } else {
        $yaml = [];
        $md_content = $content;
    }

    // Ensure $yaml is an array
    if (!is_array($yaml)) {
        $yaml = [];
    }

    // Generate a slug or unique identifier for the post
    $slug = $yaml['slug'] ?? null;
    if (!$slug) {
        // Generate slug from the title or filename
        if (!empty($yaml['title'])) {
            $title_for_slug = $yaml['title'];
            // Remove any non-alphanumeric characters (except hyphens)
            $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($title_for_slug));
            $slug = trim($slug, '-');
        } else {
            $slug = pathinfo($file, PATHINFO_FILENAME);
        }
    }

    // Ensure the slug is URL-safe
    $slug = rawurlencode($slug);

    // Sanitize title and output it safely
    $title = $yaml['title'] ?? 'Untitled';
    $title = htmlspecialchars_decode($title, ENT_QUOTES);

    // Extract the content before the readmore tag
    $content_parts = explode('<!--more-->', $md_content);
    $intro = $content_parts[0];

    // Parse Markdown with raw HTML allowed
    $html_intro = $parsedown->text($intro);

    // Display the post
    echo '<div class="post">';
    echo '<div class="avatar"></div>';
    echo '<h3><a href="post.php?slug=' . htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</a></h3>';

    $date = getPostDate($file);
    if ($date) {
        echo '<p class="date">' . htmlspecialchars($date->format('F j, Y, g:i A'), ENT_QUOTES, 'UTF-8') . '</p>';
    }

    echo $html_intro;
    if (count($content_parts) > 1) {
        // There's more content after the readmore tag
        echo '<p><a href="post.php?slug=' . htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') . '">Read more...</a></p>';
    }
    echo '</div>';
}

// Pagination controls
echo '<div class="pagination">';

if ($current_page > 1) {
    echo '<a href="?page=' . ($current_page - 1) . '">Previous</a>';
}

// Display first page and ellipsis if needed
if ($current_page > 3) {
    echo '<a href="?page=1">1</a>';
    if ($current_page > 4) {
        echo '<span>...</span>';
    }
}

// Display pages around the current page
for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++) {
    if ($i == $current_page) {
        echo '<span class="current-page">' . $i . '</span>';
    } else {
        echo '<a href="?page=' . $i . '">' . $i . '</a>';
    }
}

// Display last page and ellipsis if needed
if ($current_page < $total_pages - 2) {
    if ($current_page < $total_pages - 3) {
        echo '<span>...</span>';
    }
    echo '<a href="?page=' . $total_pages . '">' . $total_pages . '</a>';
}

if ($current_page < $total_pages) {
    echo '<a href="?page=' . ($current_page + 1) . '">Next</a>';
}

echo '</div>';


// Include the footer
include __DIR__ . '/footer.php';
?>

