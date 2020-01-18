# searchMB
searchMB (search Movies and Books) is a RESTful API to fetch information about movies from [OMDb](http://www.omdbapi.com/) and books from [OpenLibrary](https://openlibrary.org/).
# How to get started
1. Clone project to your M/A/WAMP etc. localhost folder
2. Install dependencies with `composer install`
3. Make a GET request to `localhost:PORT/searchMB/help`
4. Follow instructions

# Usage
- Search movies via `/getMovie` endpoint with parameters `title`, `year` and `plot`.
- Search books via `/getBook` endpoint with parameter `isbn`.
- Results are returned in JSON format