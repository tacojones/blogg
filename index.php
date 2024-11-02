<?php
// Include Parsedown and YAML parser
require_once __DIR__ . '/includes/Parsedown.php';
require_once __DIR__ . '/includes/YAMLParser.php';

$posts_dir = __DIR__ . '/posts/';
$posts_per_page = 10;

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

// Function to get post date from YAML front matter or file modification date
function getPostDate($file) {
    $content = file_get_contents($file);
    if ($content === false) {
        return null;
    }
    $content = str_replace("\r\n", "\n", $content);

    if (preg_match('/^---[\s\n]*(.*?)\n---/s', $content, $matches)) {
        $yaml_content = trim($matches[1]);
        $yaml = parseYAML($yaml_content);

        if (is_array($yaml) && isset($yaml['date'])) {
            $dateStr = $yaml['date'];
            $dateFormats = ['Y-m-d\TH:i:sP', 'Y-m-d\TH:i:s', 'Y-m-d H:i:s', 'Y-m-d H:i', 'Y-m-d', 'D, d M Y H:i:s O'];
            foreach ($dateFormats as $format) {
                $date = DateTime::createFromFormat($format, $dateStr);
                $errors = DateTime::getLastErrors();
                if ($date && $errors && $errors['warning_count'] == 0 && $errors['error_count'] == 0) {
                    return $date;
                }
            }
            $timestamp = strtotime($dateStr);
            if ($timestamp !== false) {
                return (new DateTime())->setTimestamp($timestamp);
            }
        }
    }
    $mtime = filemtime($file);
    return $mtime !== false ? (new DateTime())->setTimestamp($mtime) : null;
}

usort($all_files, function ($a, $b) {
    $dateA = getPostDate($a);
    $dateB = getPostDate($b);
    if ($dateA === null) return 1;
    if ($dateB === null) return -1;
    return $dateB->getTimestamp() <=> $dateA->getTimestamp();
});

// Pagination setup
$total_posts = count($all_files);
$total_pages = max(ceil($total_posts / $posts_per_page), 1);
$current_page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, [
    'options' => [
        'default' => 1,
        'min_range' => 1,
        'max_range' => $total_pages,
    ],
]);
$offset = ($current_page - 1) * $posts_per_page;
$files = array_slice($all_files, $offset, $posts_per_page);

// Parsedown setup
$parsedown = new Parsedown();
$parsedown->setSafeMode(false);

// Include header
include __DIR__ . '/header.php';

// Display posts
foreach ($files as $file) {
    $content = @file_get_contents($file);
    if ($content === false) continue;

    $content = str_replace("\r\n", "\n", $content);
    if (preg_match('/^---[\s\n]*(.*?)\n---[\s\n]*(.*)/s', $content, $matches)) {
        $yaml_content = trim($matches[1]);
        $yaml = parseYAML($yaml_content);
        $md_content = trim($matches[2]);
        $yaml = is_array($yaml) ? array_change_key_case($yaml, CASE_LOWER) : [];
    } else {
        $yaml = [];
        $md_content = $content;
    }

    $slug = isset($yaml['slug']) ? rawurlencode($yaml['slug']) : rawurlencode(pathinfo($file, PATHINFO_FILENAME));
    $title = htmlspecialchars($yaml['title'] ?? 'Untitled', ENT_QUOTES, 'UTF-8');
    $content_parts = explode('<!--more-->', $md_content);
    $html_intro = $parsedown->text($content_parts[0]);

    echo '<div class="post">';
    echo '<div class="avatar"></div>';
    echo '<h3><a href="post.php?slug=' . htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') . '">' . $title . '</a></h3>';
    
    $date = getPostDate($file);
    if ($date) {
        echo '<p class="date">' . htmlspecialchars($date->format('F j, Y, g:i A'), ENT_QUOTES, 'UTF-8') . '</p>';
    }

    echo $html_intro;
    if (count($content_parts) > 1) {
        echo '<p><a href="post.php?slug=' . htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') . '">Read more...</a></p>';
    }
    echo '</div>';
}

// Pagination controls
echo '<div class="pagination">';
if ($current_page > 1) {
    echo '<a href="?page=' . ($current_page - 1) . '">Previous</a> ';
}
for ($i = 1; $i <= $total_pages; $i++) {
    echo ($i == $current_page) ? '<span class="current-page">' . $i . '</span> ' : '<a href="?page=' . $i . '">' . $i . '</a> ';
}
if ($current_page < $total_pages) {
    echo '<a href="?page=' . ($current_page + 1) . '">Next</a>';
}
echo '</div>';

// Include footer
include __DIR__ . '/footer.php';
?>
