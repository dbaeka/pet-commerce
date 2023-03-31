# Pets Shop

Pet Store is a laravel app that provides an API for consumers 
to have the necessary endpoints and HTTP request methods to
order, view products and manage users. This app satisfies all
the endpoints requested in the main task.

## Usage
### Using the Terminal
1. Clone this repository into your local development.
2. Copy the .env.dev file to .env using
```bash
cp .env.dev .env
```
3. Run the following commands to bootstrap the application
```bash
composer install && php artisan key:generate
```
4. The application uses a DB. For testing purposes, the default db
is sqlite and the queue connection is set to sync. Run the following
to sync the db with tables and seeds
```bash
php artisan db:seed --migrate
```
5. JWT tokens require you to use RSA keys. By default the 
`config/jwt.php` file contains the default location, which is 
the root storage directory. You can set the related env variables
to switch them. To let the app generate RSA keys, run
```bash
php artisan jwt:keys
```
6. Start the app by running
```bash
php artisan serve
```
7. You can start working with the API using the swagger link below
[http://localhost:8000/api/swagger]()

### Using docker-compose

## Testing
To run tests, simply run
```bash
composer test
```

## Linting
To run the lint test, simply run
```bash
composer pint
```

## PHPStan
To run PHP stan, simply run
```bash
composer analyse
```

## PHP Insights
To run PHP Insights, simply run
```bash
composer insight
```

## Design Decisions
### Allowed Routes
Admin Not Allowed
- Create payment
- Edit payment
- Delete payment
- Edit order
- Create order
- All User Endpoints

User Not Allowed
- All Admin endpoints
- Brand, Category,Order Status secure routes
- Edit payment
- Delete payment
- Dashboard orders
- Shipped orders
- Delete order

Deleting order can better be a cancel API instead. Likewise, editing
and deleting payments, although implemented, is restricted to both users
until a well defined policy is given as to what situations a payment can
be modified. It is best to always start a new payment than to use an old one
especially for credit card payments.

### Future Design/Shortcomings
- Can have an include parameter to dynamically control addition of relations
- Use Redis cache to keep a blacklist for invalidated token JTIs
 and then having a scheduled job scrub the database 
- Masking sensititive Payment Details such as credit card details. In applications,
once the payment is made to the Payment Service API, the masked info is stored. To
keep card details, different services allow storage of authorization ids. An
alternative would be to encrypt details
- Response codes can be improved to give more details based on a specification
- Most tests have been left out since they ensure more coverage rather than testing
new functions. Rather, all feature tests were added for each endpoint. There
is room for adding more edge cases

*Note*: Calculation from order seeder might be off in invoice
