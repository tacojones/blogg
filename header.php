<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nebulung</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="container">
        <header>
            <h1 id="header">SUNDISC INVASION</h1>

    <div class="menu">
        <ul>
            <li><a href="/">Home</a></li>
            <?php
            // Directory containing .md pages
            $pagesDir = __DIR__ . '/pages';
            // Get .md files in /pages directory, excluding . and ..
            $pages = array_diff(scandir($pagesDir), ['.', '..']);

            foreach ($pages as $page) {
                // Only include .md files
                if (pathinfo($page, PATHINFO_EXTENSION) === 'md') {
                    // Extract the page name without the extension
                    $pageName = pathinfo($page, PATHINFO_FILENAME);

                    // Convert underscores or dashes to spaces and capitalize the title
                    $pageTitle = ucwords(str_replace(['-', '_'], ' ', $pageName));

                    // Generate the menu link to post.php with the page query
                    echo '<li><a href="/post.php?page=' . $page . '">' . $pageTitle . '</a></li>';
                }
            }
                    echo '<li><a href="#" onclick="toggleSearch()">Search</a></li>';
            ?>
        </ul>
    </div>
            <div class="search-box" id="searchBox">
                <form action="search.php" method="get">
                    <input type="text" name="q" placeholder="" required>
                    <input type="submit" value="Go">
                </form>
            </div>

       </header>
