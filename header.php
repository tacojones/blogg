<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nebulung</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&family=SUSE:wght@100..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">

<style>
@font-face {
	font-family: 'Drift';
	src: url('assets/drift.ttf') format('truetype');
	font-weight: normal;
	font-style: normal;
    -webkit-font-smoothing: none;
    -moz-osx-font-smoothing: none;
    font-smoothing: none;
}

@font-face {
	font-family: 'Candy';
	src: url('assets/candy.bdf') format('truetype');
	font-weight: normal;
	font-style: normal;
    -webkit-font-smoothing: none;
    -moz-osx-font-smoothing: none;
    font-smoothing: none;
}

nav ul {
   list-style-type: none; /* Remove bullet points */
   margin: 0;
   padding: 0;
   display: flex;
   justify-content: center; /* Center the menu items horizontally */
   font-family: 'Candy', 'Drift', 'Press Start 2P', cursive; /* Use the pixel font */
   width: 650px;
   background: transparent url(/assets/img/mnubck.png) top no-repeat;
   margin: 0 auto;
   margin-top: -34px;
   position: relative;
}

nav ul li {
    margin: 0 8px; /* Add space between each menu item */
}

nav ul li a {
    text-decoration: none; /* Remove underline */
    color: #273c4f99 !important; /* Link color */
    font-size: 14px; /* Small font size */
    font-weight: 400; /* Use lighter font weight */
    text-transform: uppercase; /* Make the text uppercase for clean look */
    letter-spacing: 0.2px; /* Add a little spacing between letters */
    text-shadow: 1px 1px 1px #00000030
}

/* Add hover effect */
nav ul li a:hover {
    color: #4a724e !important;
    text-shadow: 1px 1px 10px #273c4f;
}

.searchinput {
    width: 200px;
}

/* Collapsible Div */
#collapsibleDiv {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
    background: #111e26 url(../assets/img/ddbck.png);
    text-align: center;
    width: 650px;
    margin: 0 auto;
    display: flex;               /* Use flexbox for centering */
    justify-content: center;     /* Center items horizontally */
    align-items: center;  
}

#collapsibleDiv.expanded {
    max-height: 100px;
}
</style>
</head>
<body>

<div id="top"><img src="assets/img/nebuheader2.png" /></div>
 <header>
   <nav>
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
                    echo '<li><a href="#" id="toggleLink">Search</a></li>';
            ?>
        </ul>
    </nav>

<div id="collapsibleDiv" class="collapsed">
    <form action="search.php" method="get">
        <input type="text" name="q" class="searchinput" placeholder="" required>
        <button type="submit">Search</button>
    </form>
</div>
 </header>

<div class="container">
