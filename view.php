<?php
require 'Parsedown.php';
$Parsedown = new Parsedown();

// Define your desired date formats
const FILE_DATE_FORMAT = 'Y-m-d'; // Format for file names
const DISPLAY_DATE_FORMAT = 'F j, Y'; // Format for displaying post dates

// Get the file parameter and ensure it has the correct extension for security
$file = isset($_GET['file']) ? basename($_GET['file']) : '';
if (!preg_match('/^[a-zA-Z0-9-_]+\.md$/', $file)) {
    die('Invalid file name.');
}

$file_path = "posts/$file";

// Check if the file exists
if (!file_exists($file_path)) {
    die('The requested post does not exist.');
}

// Function to parse the markdown file, extracting front matter and content
function parse_markdown_file(string $file_path): array {
    if (!file_exists($file_path)) {
        return [
            'title' => 'Untitled Post',
            'date' => date(DISPLAY_DATE_FORMAT), // Use display format for missing date
            'content' => 'This post could not be found.'
        ];
    }

    $content = file_get_contents($file_path);
    list($metadata, $content) = parse_yaml_front_matter($content);
    
    // Set default title and date if not present in front matter
    $title = htmlspecialchars($metadata['title'] ?? 'Untitled Post');
    
    // Format the date from front matter to a specific format
    $date = !empty($metadata['date']) ? date(DISPLAY_DATE_FORMAT, strtotime($metadata['date'])) : date(DISPLAY_DATE_FORMAT);

    return [
        'title' => $title,
        'date' => $date,
        'content' => $content
    ];
}

// Custom function to parse YAML front matter manually
function parse_yaml_front_matter(string $content): array {
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

// Remove the <!--more--> tag from the content
$post['content'] = str_replace('<!--more-->', '', $post['content']);

include 'includes/header.php';
?>

<div class="post">
    <img class="avatar" src="images/avatar.png" alt="Avatar" />
    <h2><?= htmlspecialchars($post['title']) ?></h2>
    <div class="date"><?= htmlspecialchars($post['date']) ?></div>
    <?= $Parsedown->text($post['content']) ?>
</div>

<?php include 'includes/footer.php'; ?>
