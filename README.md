# News Aggregator

This Laravel project serves as a news aggregator, fetching and aggregating news data from multiple API sources, including The Guardian, The New York Times, and News API. The aggregated data is stored in a local database and can be accessed through various filters using the NewsRepository.

## Features

- Fetches news data from The Guardian, The New York Times, and News API.
- Aggregates and stores the data in a local database.
- Provides a flexible NewsRepository for querying and filtering news data.
- Exposes a RESTful API for accessing aggregated news data.

## Project Structure

The project follows the Repository Pattern for better organization and separation of concerns. Key components include:

- `NewsAggregatorService`: A service responsible for aggregating data from different API sources and saving it using the NewsRepository.

- `GuardianRepository`, `NYTimesRepository`, `NewsApiRepository`: Repositories for interacting with The Guardian, The New York Times, and News API, respectively.

- `NewsRepository`: A repository for handling news-related operations and providing a unified interface for querying and filtering news data.

## Getting Started

### Prerequisites

- PHP 8.1+
- Composer
- Laravel 10+

### Installation

1. Clone the repository:

```bash
git clone https://github.com/Mahdi-Abbariki/news-aggregator
cd news-aggregator
```

2. Install dependencies:

```bash
composer install
```

3. Copy the .env.example file to .env and configure your database and API credentials:

```bash
cp .env.example .env
```

4. Run migrations:

```bash
php artisan migrate
```

5. Schedule the task to update news data regularly:

```bash
* * * * * php /path-to-news-aggregator-project/artisan schedule:run >> /dev/null 2>&1
```

### Usage

To manually update news data, run the following command:
```bash
php artisan news:update
```

Access news data using the provided endpoints or integrate the NewsRepository into your controllers or services.


#### API documentation

You can also Access API documentation through this url `/api/documentation`


## License

This project is licensed under the [MIT License](LICENSE).