# PHP Blog Script

This is a simple PHP blog script that parses Markdown posts with YAML front matter for metadata, allowing you to group posts by year and month in subfolders. It also supports static pages, a clean URL structure, and automatic slug generation. The script uses [Parsedown](https://parsedown.org/) for Markdown parsing and a custom YAML parser for front matter.

## Features

- Posts are written in Markdown with optional YAML front matter for metadata.
- Group blog posts by year and month in subfolders for organized file structure.
- Slug generation for clean URLs based on the post title or filename.
- Safe file path handling to avoid directory traversal vulnerabilities.
- Static page support for `/pages` directory.
- Markdown parsing using Parsedown.
- Custom YAML parser for post metadata.
- Displays the post's date and title automatically.
- Supports custom date formatting and multiple formats for flexibility.
- Handles special `<!--more-->` tag for controlling content display.

## File Structure

    /posts/ ├── YYYY/ │ └── MM/ │ └── post-slug.md /pages/ └── about.md /includes/ ├── Parsedown.php └── YAMLParser.php post.php header.php footer.php


- `posts/`: Contains blog posts grouped by year (`YYYY`) and month (`MM`).
- `pages/`: Contains static pages like "about.md".
- `Parsedown.php`: Handles Markdown parsing.
- `YAMLParser.php`: Parses YAML front matter.
- `post.php`: Main blog script that handles rendering both posts and static pages.

## Usage

1. **Clone the repository**:
   ```bash
   git clone https://github.com/your-username/php-blog-script.git

2. **Setup posts and pages**:

 - Add your blog posts in the /posts/YYYY/MM/ folder structure. Each post should be a .md file with optional YAML front matter for metadata (e.g., title, date, slug).
 - Add any static pages in the /pages/ folder.

Example post format:
```markdown
---
title: "My First Post"
date: 2024-10-23
slug: my-first-post
---
Content of the post goes here in Markdown.
```

3. **Access posts and pages**:

 - Posts are accessible via post.php?slug=post-slug.
 - Pages are accessible via post.php?page=page-filename.

4. **Customizing date formats**: The script supports multiple date formats for flexibility in displaying post dates. Modify this section in post.php to add or change formats.

## Dependencies

 - [Parsedown](https://parsedown.org/): A fast Markdown parser.
 - Custom YAML parser.

## Security

 - The script includes file path sanitization using the safeFilePath() function to prevent directory traversal attacks.
 - Be sure to keep your server and PHP environment up-to-date to avoid security risks.

## Customization

 - You can modify the header.php and footer.php files to customize the layout and styling of the blog.
 - To add new features, such as pagination, archives, or additional metadata fields, modify the logic in post.php.

## License

This project is open source and available under the MIT License.


## Contributing

Contributions, issues, and feature requests are welcome! Feel free to check the [issues page](https://github.com/your-username/php-blog-script/issues).


