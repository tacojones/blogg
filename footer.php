<footer>
    <p>&copy; 2024 My PHP Blog. All rights reserved.</p>
</footer>
</div>
<div id="btm"></div>

<!-- Include highlight.js -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/atom-one-dark.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
<script>hljs.highlightAll();</script>

<!--
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Select all elements with the classes .post-text and .post-tags
    const textContainers = document.querySelectorAll('.post, .post-tags');

    textContainers.forEach(container => {
        // Replace text with spans for tags wrapped in anchor tags
        container.innerHTML = container.innerHTML.replace(/#(\w+)/g, '<a href="#" class="tag">#$1</a>');
    });
});
</script>
-->
<script>
document.getElementById('toggleLink').addEventListener('click', function(event) {
    event.preventDefault();
    var collapsibleDiv = document.getElementById('collapsibleDiv');
    if (collapsibleDiv.classList.contains('expanded')) {
        collapsibleDiv.classList.remove('expanded');
        collapsibleDiv.classList.add('collapsed');
    } else {
        collapsibleDiv.classList.remove('collapsed');
        collapsibleDiv.classList.add('expanded');
    }
});
</script>

</body>
</html>

