<?php
session_start();

// Initialize watchlist if it doesn't exist
if (!isset($_SESSION['watchlist'])) {
    $_SESSION['watchlist'] = [];
}

$watchlist = $_SESSION['watchlist'];
$animeData = [];

// Fetch data for each anime ID in the watchlist from AniList API
foreach ($watchlist as $animeId) {
    $query = [
        'query' => '
            query ($id: Int) {
                Media(id: $id, type: ANIME) {
                    id
                    title {
                        romaji
                    }
                    coverImage {
                        large
                    }
                }
            }
        ',
        'variables' => ['id' => $animeId]
    ];

    $ch = curl_init('https://graphql.anilist.co');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($query));
    curl_setopt($ch, CURLOPT_POST, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if (isset($data['data']['Media'])) {
        $animeData[] = $data['data']['Media'];
    }
}

// Handle removal from watchlist
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_id'])) {
    $removeId = intval($_POST['remove_id']);
    $_SESSION['watchlist'] = array_filter($_SESSION['watchlist'], fn($id) => $id !== $removeId);
    header("Location: watchlist.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Watchlist | AniTrack</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .anime-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
      gap: 1rem;
    }
    .anime-card {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: center;
    }
    .remove-btn {
      background: red;
      color: white;
      border: none;
      padding: 5px 10px;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">AniTrack</div>
    <nav>
      <a href="homepage.php">Home</a>
      <a href="anime-list.php">Anime List</a>
    </nav>
  </header>

  <main>
    <section class="watchlist">
      <h2>Your Watchlist</h2>

      <?php if (empty($animeData)): ?>
        <p>Your watchlist is empty.</p>
      <?php else: ?>
        <div class="anime-grid">
          <?php foreach ($animeData as $anime): ?>
            <div class="anime-card">
              <img src="<?= htmlspecialchars($anime['coverImage']['large']) ?>" alt="<?= htmlspecialchars($anime['title']['romaji']) ?>">
              <p><?= htmlspecialchars($anime['title']['romaji']) ?></p>
              <form method="POST">
                <input type="hidden" name="remove_id" value="<?= $anime['id'] ?>">
                <button class="remove-btn" type="submit">Remove</button>
              </form>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </main>

  <footer>
    <p>&copy; 2025 AniTrack</p>
  </footer>
</body>
</html>
