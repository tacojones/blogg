<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogg</title>
    <link rel="stylesheet" type="text/css" href="includes/styles.css">
</head>
<body>

    <div class="container">
        <a href="index.php"><div class="avatar"></div></a>
		<header><h1><a href="index.php">Neo's Hideout</a></h1></header>
        <div class="menu">
		    <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <!--<li><a href="post.php">Post</a></li>-->
                <li><a href="#" id="toggleLink">Search</a></li>
            </ul>
        </div>
        <div id="collapsibleDiv" class="collapsed">
        <div class="search">
        <form method="GET" action="index.php">
            <input type="text" name="search" placeholder="" class="searchinput" value="\\\">
            <button type="submit">Search</button>
        </form>
        </div>
		</div>
        <main>
            <section>
