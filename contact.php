<?php
include 'includes/header.php';
?>
		
<style>
        .column {
            flex: 1;
            min-width: 280px;
            margin: 0 auto;
            
        }
        .contact-info, .social-media {
            margin-bottom: 30px;
            margin-left: 60px;
            font-size: 1rem;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .contact-info p strong, .social-media a strong {
    background: -webkit-linear-gradient(#5f855d, #40593f);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
        }

        .social-media a:hover {
            color: #00cc66;
        }
</style>

            <img style="float: right; opacity: 0.7; margin-right: 35px;" src="photo.png">
        <div class="column contact-info">
            <h3>Contact Information</h3>
            <p><strong>Email:</strong> contact@example.com</p>
            <p><strong>Phone:</strong> +123 456 7890</p>
            <p><strong>Address:</strong> 123 Street, City, Country</p>
        </div>
        <div class="column social-media">
            <h3>Follow Us</h3>
            <p><a href="https://twitter.com/yourprofile" target="_blank"><strong>Twitter</strong>: @yourprofile</a></p>
            <p><a href="https://facebook.com/yourpage" target="_blank"><strong>Facebook</strong>: Your Page</a></p>
            <p><a href="https://instagram.com/yourprofile" target="_blank"><strong>Instagram</strong>: @yourprofile</a></p>
            <p><a href="https://linkedin.com/in/yourprofile" target="_blank"><strong>LinkedIn</strong>: Your Profile</a></p>
        </div>


<?php
include 'includes/footer.php';
?>
