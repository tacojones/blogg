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
</body>
</html>
