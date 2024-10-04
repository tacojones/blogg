<?php
require 'Parsedown.php';

$Parsedown = new Parsedown();
$Parsedown->setSafeMode(true); // Prevents unsafe HTML output

// Define your desired date formats
const FILE_DATE_FORMAT    = 'Y-m-d';    // Format for file names
const DISPLAY_DATE_FORMAT = 'F j, Y';   // Format for displaying post dates

// Get the 'file' parameter securely
$file = filter_input(INPUT_GET, 'file', FILTER_SANITIZE_STRING) ?? '';

if (empty($file) || !preg_match('/^[a-zA-Z0-9-_]+\.md$/', $file)) {
    http_response_code(400);
    include 'includes/400.php'; // Custom 400 Bad Request error page
    exit;
}

$file_path = realpath(__DIR__ . "/posts/$file");

// Check if the file exists and is within the 'posts' directory
$posts_dir = realpath(__DIR__ . '/posts');

if (!$file_path || strpos($file_path, $posts_dir) !== 0) {
    http_response_code(404);
    include 'includes/404.php'; // Custom 404 Not Found error page
    exit;
}

// Function to parse the markdown file, extracting front matter and content
function parse_markdown_file(string $file_path): array {
    if (!file_exists($file_path)) {
        return [
            'title'   => 'Untitled Post',
            'date'    => date(DISPLAY_DATE_FORMAT), // Use display format for missing date
            'content' => 'This post could not be found.',
        ];
    }

    $content = file_get_contents($file_path);
    list($metadata, $content) = parse_yaml_front_matter($content);

    // Set default title if not present in front matter
    $title = $metadata['title'] ?? 'Untitled Post';

    // Parse the date from front matter
    $date_str  = $metadata['date'] ?? '';
    $timestamp = strtotime($date_str);

    if ($timestamp) {
        $date = date(DISPLAY_DATE_FORMAT, $timestamp);
    } else {
        // Use file modification time if date is not valid
        $timestamp = filemtime($file_path);
        $date      = date(DISPLAY_DATE_FORMAT, $timestamp);
    }

    return [
        'title'   => $title,
        'date'    => $date,
        'content' => $content,
    ];
}

// Custom function to parse YAML front matter manually
function parse_yaml_front_matter(string $content): array {
    $metadata = [];
    if (preg_match('/^---\s*(.*?)\s*---/s', $content, $matches)) {
        $lines = preg_split('/\r\n|\n|\r/', trim($matches[1]));
        foreach ($lines as $line) {
            if (preg_match('/^\s*([^\s:]+)\s*:\s*(.*?)\s*$/', $line, $mv)) {
                $key   = trim($mv[1]);
                $value = trim($mv[2], " '\""); // Remove quotes
                $metadata[$key] = $value;
            }
        }
        // Remove front matter from content
        $content = trim(substr($content, strlen($matches[0])));
    }
    return [$metadata, $content];
}

// Parse the requested markdown file
$post = parse_markdown_file($file_path);

// Remove the <!--more--> tag from the content
$post['content'] = str_replace('<!--more-->', '', $post['content']);

include 'includes/header.php';
?>

<div class="post">
    <img class="avatar" src="images/avatar.png" alt="Avatar" />
    <h2><?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?></h2>
    <div class="date"><?= htmlspecialchars($post['date'], ENT_QUOTES, 'UTF-8') ?></div>
    <?= $Parsedown->text($post['content']) ?>
</div>

<?php include 'includes/footer.php'; ?>
