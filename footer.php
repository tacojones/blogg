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
 <script>
 // Store player instances in a map, using each player's unique id
const players = {};

function onYouTubeIframeAPIReady() {
    const mediaPlayers = document.querySelectorAll('.media-player');

    mediaPlayers.forEach((playerDiv, index) => {
        const videoId = playerDiv.dataset.video;
        const uniquePlayerId = `yt-player-${index}`;

        // Create a hidden div to house the iframe player
        const playerContainer = document.createElement('div');
        playerContainer.id = uniquePlayerId;
        playerContainer.style.display = 'none';
        document.getElementById('youtube-players').appendChild(playerContainer);

        // Initialize the YouTube player instance and store it in the players map
        players[uniquePlayerId] = new YT.Player(uniquePlayerId, {
            height: '0',
            width: '0',
            videoId: videoId,
            playerVars: {
                'playsinline': 1,
                'controls': 0
            },
            events: {
                onReady: (event) => setupControls(event, playerDiv, uniquePlayerId)
            }
        });
    });
}

function setupControls(event, playerDiv, uniquePlayerId) {
    const playerInstance = players[uniquePlayerId];
    const togglePlayBtn = playerDiv.querySelector('.toggle-play-btn');
    const stopBtn = playerDiv.querySelector('.stop-btn');
    const progressBar = playerDiv.querySelector('.progress-bar');
    const elapsedTimeDisplay = playerDiv.querySelector('.elapsed-time');
    const totalTimeDisplay = playerDiv.querySelector('.total-time');

    // Track the play/pause state
    let isPlaying = false;

    // Get the total duration and display it
    playerInstance.addEventListener('onReady', () => {
        const duration = playerInstance.getDuration();
        totalTimeDisplay.textContent = formatTime(duration);
    });

    // Toggle play/pause button
    togglePlayBtn.addEventListener('click', () => {
        if (isPlaying) {
            playerInstance.pauseVideo();
            // Set the button to the play icon
            togglePlayBtn.innerHTML = `
                <svg class="play-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                </svg>
            `;
            console.log(`Paused video ID: ${playerInstance.getVideoData().video_id}`);
        } else {
            playerInstance.playVideo();
            // Set the button to the pause icon
            togglePlayBtn.innerHTML = `
                <svg class="pause-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M6 6h4v12H6zm8 0h4v12h-4z"/>
                </svg>
            `;
            console.log(`Playing video ID: ${playerInstance.getVideoData().video_id}`);
        }
        isPlaying = !isPlaying;
    });

    // Stop button action
    stopBtn.addEventListener('click', () => {
        playerInstance.stopVideo();
        progressBar.value = 0; // Reset progress bar
        elapsedTimeDisplay.textContent = "0:00"; // Reset elapsed time display
        togglePlayBtn.innerHTML = `
            <svg class="play-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z"/>
            </svg>
        `;
        isPlaying = false;
        console.log(`Stopped video ID: ${playerInstance.getVideoData().video_id}`);
    });

    // Update the progress bar and elapsed time periodically
    setInterval(() => {
        const duration = playerInstance.getDuration();
        const currentTime = playerInstance.getCurrentTime();
        if (duration > 0) {
            progressBar.value = (currentTime / duration) * 100;
            elapsedTimeDisplay.textContent = formatTime(currentTime);
        }
    }, 1000);

    // Allow scrubbing via the progress bar
    progressBar.addEventListener('input', (e) => {
        const duration = playerInstance.getDuration();
        const newTime = (e.target.value / 100) * duration;
        playerInstance.seekTo(newTime);
        console.log(`Seeked video ID: ${playerInstance.getVideoData().video_id} to ${newTime}s`);
    });
}

// Helper function to format time from seconds to MM:SS
function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${minutes}:${secs < 10 ? '0' : ''}${secs}`;
}

</script>

</body>
</html>

