function getCurrentSeason() {
    const now = new Date();
    const month = now.getMonth(); // 0 = Jan, 11 = Dec
    const year = now.getFullYear();
  
    let season;
    if (month >= 0 && month <= 2) {
      season = 'WINTER';
    } else if (month >= 3 && month <= 5) {
      season = 'SPRING';
    } else if (month >= 6 && month <= 8) {
      season = 'SUMMER';
    } else {
      season = 'FALL';
    }
  
    return { season, year };
  }
  
  function generateQuery(season, year) {
    return `
      query {
        Page(perPage: 6) {
          trending: media(sort: TRENDING_DESC, type: ANIME) {
            id
            title {
              romaji
            }
            coverImage {
              large
            }
          }
        }
  
        Season: Page(perPage: 6) {
          seasonal: media(season: ${season}, seasonYear: ${year}, type: ANIME, sort: POPULARITY_DESC) {
            id
            title {
              romaji
            }
            coverImage {
              large
            }
          }
        }
      }
    `;
  }
  
  async function fetchAnime() {
    const url = 'https://graphql.anilist.co';
    const { season, year } = getCurrentSeason();
    const query = generateQuery(season, year);
  
    try {
      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({ query })
      });
  
      const result = await response.json();
  
      displayAnime(result.data.Season.seasonal, "seasonal-list");
      displayAnime(result.data.Page.trending, "trending-list");
  
    } catch (error) {
      console.error("Failed to fetch AniList data:", error);
    }
  }
  
  function displayAnime(animeList, containerId) {
    const container = document.getElementById(containerId);
    container.innerHTML = "";
  
    animeList.forEach(anime => {
      const card = document.createElement("div");
      card.classList.add("anime-card");
  
      const link = document.createElement("a");
      link.href = `anime-detail.php?id=${anime.id}`; // link to the anime detail page
  
      card.innerHTML = `
        <img src="${anime.coverImage.large}" alt="${anime.title.romaji}">
        <p>${anime.title.romaji}</p>
      `;
  
      link.appendChild(card); 
      container.appendChild(link); 
    });
  }
  
  document.addEventListener("DOMContentLoaded", fetchAnime);
  