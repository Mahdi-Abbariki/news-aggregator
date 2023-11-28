# News Aggregator

This Laravel project serves as a news aggregator, fetching and aggregating news data from multiple API sources, including The Guardian, The New York Times, and News API. The aggregated data is stored in a local database and can be accessed through various filters using the implemented API.

## Features

- **News Sources Integration**: Fetches news data seamlessly from popular sources, including The Guardian, The New York Times, and News API.
- **Data Aggregation and Storage**: Efficiently aggregates and stores the fetched data in a local database, ensuring a centralized and organized news repository.
- **Flexible NewsRepository**: Employs a versatile NewsRepository allowing easy and standardized querying of news data, providing a clean abstraction for data interactions.
- **RESTful API**: Offers a RESTful API for convenient access to aggregated news data, providing a straightforward means for integration with other applications or services.
- **Design Patterns Implementation**: Utilizes design patterns to maintain consistency and ensure a structured, scalable, and maintainable codebase.

## Project Structure

The project adopts the Repository and Strategy Patterns to enhance organization and maintain separation of concerns. Noteworthy components include:

- `NewsRepository`: A repository centralizing news-related operations, offering a unified interface for querying news data. The repository is responsible for fetching updated news from all defined strategies specified in `config/news.php`.

- Interfaces (`NewsApiStrategyInterface`, `NewsableInterface`): Implemented by all strategies, these interfaces ensure a standardized approach for interacting with various news sources, promoting consistency and ease of integration.

- `GuardianStrategy`,  `NYTimesStrategy`,  `NewsApiStrategy`: Strategies designed for interacting with specific news sources such as The Guardian, The New York Times, and News API, respectively.

- `NewsController`: Manages APIs for news-related functionalities, providing endpoints for retrieving aggregated news data. This controller access news stored in the local database, ensuring efficient and reliable data retrieval for API consumers.

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

4. Add related API Keys in .env

5. Run migrations:

```bash
php artisan migrate
```

6. Schedule the task to update news data regularly:

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