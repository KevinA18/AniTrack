<?php
session_start();

// Initialize watchlist if it doesn't exist
if (!isset($_SESSION['watchlist'])) {
    $_SESSION['watchlist'] = [];
}

$anime_id = isset($_GET['id']) ? intval($_GET['id']) : null;
if (!$anime_id) {
    echo "Anime not found!";
    exit;
}

// Check if anime is already in watchlist
$isInWatchlist = in_array($anime_id, $_SESSION['watchlist']);

$query = '
  query ($id: Int) {
    Media(id: $id) {
      id
      title {
        romaji
        english
        native
      }
      description(asHtml: false)
      coverImage {
        large
      }
      episodes
      genres
      format
      status
      startDate {
        year
        month
        day
      }
      trailer {
        site
        id
      }
    }
  }
';

$url = 'https://graphql.anilist.co';
$headers = [
    'Content-Type: application/json',
    'Accept: application/json'
];

$data = json_encode([
    'query' => $query,
    'variables' => ['id' => $anime_id]
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_POST, true);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
$anime = $result['data']['Media'];

function safeDescription($text) {
    return nl2br(htmlspecialchars(strip_tags($text)));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= $anime['title']['romaji'] ?> | AniTrack</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <header>
    <div class="logo">AniTrack</div>
    <nav>
      <a href="homepage.php">Home</a>
      <a href="anime-list.php">Anime List</a>
      <a href="watchlist.php">Watchlist</a>
    </nav>
  </header>

  <main>
    <section class="anime-detail">
      <img src="<?= $anime['coverImage']['large'] ?>" alt="<?= $anime['title']['romaji'] ?>" />

      <div class="info">
        <h1><?= $anime['title']['romaji'] ?></h1>
        <h3><?= $anime['title']['english'] ?> | <?= $anime['title']['native'] ?></h3>

        <p><strong>Episodes:</strong> <?= $anime['episodes'] ?? 'TBA' ?></p>
        <p><strong>Format:</strong> <?= $anime['format'] ?></p>
        <p><strong>Status:</strong> <?= $anime['status'] ?></p>
        <p><strong>Start Date:</strong>
          <?= $anime['startDate']['year'] ?>-<?= str_pad($anime['startDate']['month'], 2, "0", STR_PAD_LEFT) ?>-<?= str_pad($anime['startDate']['day'], 2, "0", STR_PAD_LEFT) ?>
        </p>
        <p><strong>Genres:</strong> <?= implode(', ', $anime['genres']) ?></p>

        <p><strong>Description:</strong><br><?= safeDescription($anime['description']) ?></p>

        <!-- Watchlist Button -->
        <?php if ($isInWatchlist): ?>
          <button class="watchlist-btn" disabled>✅ Already in Watchlist</button>
        <?php else: ?>
          <form action="add-to-watchlist.php" method="GET" style="margin-top: 20px;">
            <input type="hidden" name="id" value="<?= $anime['id'] ?>">
            <button type="submit" class="watchlist-btn">➕ Add to Watchlist</button>
          </form>
        <?php endif; ?>

        <?php if (!empty($anime['trailer']) && $anime['trailer']['site'] === 'youtube'): ?>
          <div class="trailer" style="margin-top: 20px;">
            <h3>Trailer</h3>
            <iframe width="560" height="315" src="https://www.youtube.com/embed/<?= $anime['trailer']['id'] ?>" frameborder="0" allowfullscreen></iframe>
          </div>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <footer>
    <p>&copy; 2025 AniTrack</p>
  </footer>
</body>
</html>
