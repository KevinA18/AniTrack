<?php
// Optional: Get search term from URL
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Define the GraphQL query
$query = '
query ($search: String) {
  Page(perPage: 20) {
    media(search: $search, type: ANIME) {
      id
      title {
        romaji
      }
      coverImage {
        large
      }
    }
  }
}';

// Prepare request payload
$variables = ['search' => $search];
$payload = json_encode(['query' => $query, 'variables' => $variables]);

// Send request
$ch = curl_init('https://graphql.anilist.co');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
$response = curl_exec($ch);
curl_close($ch);

// Decode response
$data = json_decode($response, true);
$animeList = $data['data']['Page']['media'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Anime List | AniTrack</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <header>
    <div class="logo">AniTrack</div>
    <nav>
      <a href="homepage.php">Home</a>
      <a href="watchlist.php">Watchlist</a>
    </nav>
  </header>

  <main>
    <section class="anime-list">
      <h2>Anime List</h2>

      <!-- Search Form -->
      <form method="GET" action="anime-list.php">
        <input type="text" name="search" placeholder="Search for anime..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
      </form>

      <?php if (!empty($animeList)): ?>
        <div class="anime-grid">
          <?php foreach ($animeList as $anime): ?>
            <div class="anime-card">
              <a href="anime-detail.php?id=<?= $anime['id'] ?>">
                <img src="<?= $anime['coverImage']['large'] ?>" alt="<?= htmlspecialchars($anime['title']['romaji']) ?>">
                <p><?= htmlspecialchars($anime['title']['romaji']) ?></p>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p>No anime found.</p>
      <?php endif; ?>
    </section>
  </main>

  <footer>
    <p>&copy; 2025 AniTrack</p>
  </footer>
</body>
</html>
