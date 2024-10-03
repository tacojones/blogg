<?php include 'includes/header.php'; ?>

<div class="post">
    <h2 class="green">Blogg</h2>
    <p><strong>Blogg</strong> is a simple flat-file blogging script written in PHP. It allows users to display static blog entries with subjects, date and time stamps, and supports pagination and markdown.</p>
    
    <h3>Features:</h3>
    <ul>
        <li><strong>Flat-File Storage</strong>: Posts are stored as individual text files.</li>
        <li><strong>Markdown</strong>: Blogg features markdown support with Parsedown.</li>
        <li><strong>Pagination</strong>: Blogg supports pagination for browsing posts.</li>
        <li><strong>Read More</strong>: Blogg supports truncation with a "Read More" link for long articles.</li>
        <li><strong>Search</strong>: Blogg supports text searches via a search form.</li>
    </ul>

    <p>
        <a href="https://github.com/tacojones/blogg" target="_blank" rel="noopener noreferrer">
            <?= htmlspecialchars('https://github.com/tacojones/blogg') ?>
        </a>
    </p>
</div>

<?php include 'includes/footer.php'; ?>
