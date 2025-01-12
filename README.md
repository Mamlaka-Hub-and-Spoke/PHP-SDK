
# Mamlaka SDK

Mamlaka SDK is a PHP library for integrating with the Mamlaka API.

## Installation

Install using Composer:

```bash
composer require mama-laka/payment-sdk
```

## Usage

### 1. Set up the MamlakaAPI object and authenticate

```php
$api = new MamlakaAPI('production');
$response1 = $api->getToken('username', 'password');
if(!$response1['error']){
    $response = $api->initiateMobilePayment(
        'merchantid',
        'KES',
        1.0,
        '254768899729',
        'M-Pesa',
        'externalId3',
        'https://b8ca-217-21-116-242.ngrok-free.app'
    );
    print_r($response);
} else {
    echo "Authentication failed
";
}
```


### 3. Example API Methods

#### 3.1. `getToken`
Authenticate and get the token:

```php
$response = $api->getToken('username', 'password');
```

#### 3.2. `initiateMobilePayment`
Initiate a mobile payment:

```php
$response = $api->initiateMobilePayment(
    'merchantid',
    'KES',
    1.0,
    '254768899729',
    'M-Pesa',
    'externalId',
    'https://b8ca-217-21-116-242.ngrok-free.app'
);
```

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.