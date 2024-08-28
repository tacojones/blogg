# blogg: A Flat-File Blog Script

This is a simple flat-file blog script written in PHP. It allows users to display static blog entries with subjects, date and time stamps, and supports markdown, pagination and search. I wanted a simple, portable and searchable database of blog posts and couldn't find a solution, so I made one with everything I needed and nothing more.

## Features

- **Flat-File Storage**: Posts are stored as individual text files in a directory
- **Markdown**: blogg features markdown support with Parsedown
- **Pagination**: blogg supports pagination for browsing posts
- **Read More**: blogg supports truncation with a Read More link for long articles
- **Search**: blogg supports text searches via a search form


An example of a form to post articles to your blog
```PHP
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['content'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $date = date('Y-m-d H:i:s');
    $filename = time() . '.json';
    $data = json_encode(['title' => $title, 'content' => $content, 'date' => $date, 'filename' => $filename]);
    file_put_contents("posts/$filename", $data);
    header('Location: index.php');
    exit;
}
?>

<?php
include 'includes/header.php';
?>
      <div class="post">
        <form method="POST" action="post.php">
            <label for="title">Title:</label><br>
            <input type="text" id="title" name="title" required><br>
            <label for="content">Content:</label><br>
            <textarea id="content" name="content" rows="10" required></textarea><br>
            <button type="submit">Post</button>
            <div class="back-link"></div>
        </form>
        </div>

<?php
include 'includes/footer.php';
?>
```

![image info](blogg_screenshot.png)

![image info](blogg_screenshot2.png)

![image info](screen2.png)

## License

This project is licensed under the MIT License. See the LICENSE file for more details.
