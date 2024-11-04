<footer>
    <p>&copy; 2024 My PHP Blog. All rights reserved.</p>
</footer>
</div>

<!-- Include highlight.js -->
<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/atom-one-dark.min.css">-->
<link rel="stylesheet" href="assets/css/highlight.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
<script>hljs.highlightAll();</script>

    <script>
        function toggleSearch() {
            const searchBox = document.getElementById("searchBox");
            searchBox.classList.toggle("show");
        }

document.addEventListener("DOMContentLoaded", function() {
    const posts = document.querySelectorAll('.post');
    if (posts.length > 0) {
        posts[posts.length - 1].classList.add('last-post');
    }
});


document.addEventListener("DOMContentLoaded", function() {
    const header = document.getElementById("header");
    const text = header.textContent;
    header.innerHTML = ''; // Clear header content

    // Colors to toggle
    const colors = ['#9af369', '#8c7dff'];

    // Wrap each letter in a span
    [...text].forEach(letter => {
        const span = document.createElement("span");
        span.classList.add("letter");
        span.textContent = letter;
        header.appendChild(span);
    });

    // Function to randomly set each letter's color
    function randomizeColors() {
        const letters = document.querySelectorAll("#header .letter");
        letters.forEach(letter => {
            const randomColor = colors[Math.floor(Math.random() * colors.length)];
            letter.style.color = randomColor;
        });
    }

    // Initial random colors and periodic updates
    randomizeColors();
    setInterval(randomizeColors, 300); // Adjust interval as desired
});

    </script>

<script src="https://www.youtube.com/iframe_api"></script>
<script src="assets/js/ytplayer.js"></script>

</body>
</html>

