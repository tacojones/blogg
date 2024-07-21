# Flat-File Blog Script

This is a simple flat-file blog script written in PHP. It allows users to post blog entries with subjects, date and time stamps, and supports pagination and autolinking of URLs. The blog has a modern, dark blue design with a one-column layout.

## Features

- **Flat-File Storage**: Posts are stored as individual text files in a directory.
- **Subjects**: Each post has a subject line.
- **Date and Time Stamps**: Each post includes the date and time it was posted.
- **Pagination**: The blog supports pagination for browsing posts.
- **Autolinking**: URLs in posts are automatically converted into clickable links.
- **Modern, Dark Design**: The blog features a dark blue theme that is easy on the eyes.
- **One-Column Layout**: The layout is centered and single-column for simplicity and readability.
- **Separate Posting Page**: Includes a separate page for posting new entries.

## Directory Structure

/blog
    index.php
    post.php
    style.css
    /posts
        2024-07-05-12-00-00.txt
        2024-07-06-13-00-00.txt
    /includes
        functions.php
        header.php
        footer.php


## Setup

    Clone the Repository

    git clone https://github.com/yourusername/flat-file-blog.git
    cd flat-file-blog

Create the Posts Directory
Ensure that the posts directory exists and is writable by the web server.

    mkdir posts
    chmod 777 posts

    Run the Blog
        Open index.php in your web browser to view the blog.
        Open post.php to post a new entry.

## Files
index.php

    Displays the list of blog posts with pagination.
    Each post includes a subject, date and time stamp, and content.

post.php

    Provides a form for creating new blog entries with a subject and content.

style.css

    Contains the CSS for the dark blue theme and layout.

includes/functions.php

    Contains utility functions, such as autolinking URLs.

includes/header.php

    Contains the HTML code for the header.

includes/footer.php

    Contains the HTML code for the footer.

Example Post Format

Each post is stored as a text file with the following format:

    Subject Line
    Post content goes here.

## License

This project is licensed under the MIT License. See the LICENSE file for more details.
