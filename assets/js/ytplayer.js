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

    // Get and display the total duration when the player is ready
    const duration = playerInstance.getDuration();
    totalTimeDisplay.textContent = formatTime(duration);

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
        const currentTime = playerInstance.getCurrentTime();
        if (duration > 0) {
            progressBar.value = (currentTime / duration) * 100;
            elapsedTimeDisplay.textContent = formatTime(currentTime);
        }
    }, 1000);

    // Allow scrubbing via the progress bar
    progressBar.addEventListener('input', (e) => {
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

