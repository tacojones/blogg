        <footer>
            <p>&copy; 2024 toneblogg. All rights reserved.</p>
        </footer>
    </div>
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/atom-one-dark.min.css">



<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
<script>hljs.highlightAll();</script>
</body>
</html>
