<?php
session_start();
include("connect.php");

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>AniTrack - Home</title>
  <link rel="stylesheet" href="style/style.css" />
</head>
<body>
  <header>
    <div class="logo">AniTrack</div>
    <nav>
      <a href="anime-list.php">Anime List</a>
      <a href="watchlist.php">Watchlist</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <main>
    <section class="hero">
      <h1>Track your favorite anime. Discover new ones.</h1>
    </section>

    <section class="seasonal">
      <h2>🌸 Seasonal Anime</h2>
      <div class="anime-grid" id="seasonal-list">
        <!-- Dynamically filled -->
      </div>
    </section>

    <section class="trending">
      <h2>🔥 Trending Anime</h2>
      <div class="anime-grid" id="trending-list">
        <!-- Dynamically filled -->
      </div>
    </section>
  </main>

  <footer>
    <p>&copy; 2025 AniTrack</p>
  </footer>

  <script src="home.js"></script>
</body>
</html>
