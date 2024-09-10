<?php
require 'Parsedown.php';
$Parsedown = new Parsedown();

// Get the file parameter and ensure it has the correct extension for security
$file = $_GET['file'] ?? '';
if (!preg_match('/^[a-zA-Z0-9-_]+\.md$/', $file)) {
    die('Invalid file name.');
}

$file_path = "posts/$file";

// Check if the file exists
if (!file_exists($file_path)) {
    die('The requested post does not exist.');
}

// Function to parse the markdown file, extracting front matter and content
function parse_markdown_file($file_path) {
    $content = file_get_contents($file_path);
    list($metadata, $content) = parse_yaml_front_matter($content);
    
    // Set default title and date if not present in front matter
    $title = $metadata['title'] ?? 'Untitled Post';
    $date = $metadata['date'] ?? date('Y-m-d');
    
    return [
        'title' => $title,
        'date' => $date,
        'content' => $content
    ];
}

// Custom function to parse YAML front matter manually
function parse_yaml_front_matter($content) {
    $metadata = [];
    if (preg_match('/^---\s*(.*?)\s*---/s', $content, $matches)) {
        $lines = explode("\n", trim($matches[1]));
        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                list($key, $value) = explode(':', $line, 2);
                $metadata[trim($key)] = trim($value, " '\""); // Remove quotes
            }
        }
        // Remove front matter from content
        $content = str_replace($matches[0], '', $content);
    }
    return [$metadata, $content];
}

// Parse the requested markdown file
$post = parse_markdown_file($file_path);

include 'includes/header.php';
?>

<div class="post">
    <img class="avatar" src="avatar.png" alt="Avatar" />
    <h2><?= htmlspecialchars($post['title']) ?></h2>
    <div class="date"><?= htmlspecialchars($post['date']) ?></div>
    <div class="content"><?= $Parsedown->text(htmlspecialchars($post['content'])) ?></div>
</div>

<?php
include 'includes/footer.php';
?>

