# Laravel MoRe

> Implementation of the Repository Pattern using Laravel Model binding

## The Repository Pattern

Repositories are used to wrap the logic to access the data sources of our applications.
They can be used to improve the maintainability of an application by providing a central
point in the code where the data sources are accessed.

## Installation

```
composer require wendelladriel/laravel-more
```

You can publish the config file with:

```
php artisan vendor:publish --provider="WendellAdriel\LaravelMore\LaravelMoreServiceProvider" --tag=config
```

## Usage

This package provides a `BaseRepository` class that you can extend to create your own Repositories.

Example:

```php
<?php

namespace App\Repositories;

use App\Models\User;
use WendellAdriel\LaravelMore\BaseRepository;

class UserRepository extends BaseRepository
{
    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        parent::__construct($user);
    }
}
```

By creating a class like the one above you will have access to all the methods below.

### Get All Records

To get all the records you can use the `getAll` method:

```php
/**
 * Gets all models
 * 
 * @param array $columns
 * @return Collection
 */
public function getAll(array $columns = self::ALL_COLUMNS): Collection
```

Examples:

```php
// GET ALL RECORDS WITH ALL COLUMNS
$this->userRepository->getAll();

// GET ALL RECORDS WITH SPECIFIC COLUMNS
$this->userRepository->getAll(['id', 'email']);
```

### Get All Records By Attribute

To get all the records matching an attribute you can use the `getAllBy` method:

```php
/**
 * Gets all models by the given attribute
 *
 * @param string $attribute
 * @param mixed  $value
 * @param string $compareType
 * @param bool   $withTrash
 * @return Collection
 */
public function getAllBy(string $attribute, $value, string $compareType = '=', bool $withTrash = false): Collection
```

Examples:

```php
$this->userRepository->getAllBy('is_active', true);

$this->userRepository->getAllBy('type', ['admin', 'manager'], 'IN');
```

### Get Single Record By Attribute

To get a single record matching an attribute you can use the `getBy` method:

```php
/**
 * Gets a model by the given attribute
 *
 * @param string $attribute
 * @param mixed  $value
 * @param string $compareType
 * @param bool   $withTrash
 * @return Model|null
 */
public function getBy(string $attribute, $value, string $compareType = '=', bool $withTrash = false): ?Model
```

Examples:

```php
$this->userRepository->getBy('id', 1);

$this->userRepository->getBy('email', '%@gmail.com', 'LIKE');
```

### Get Single Record By Attribute Or Fail

To get a single record matching an attribute or throw an exception if no record is found
you can use the `getByOrFail` method:

```php
/**
 * Gets a model by the given attribute or throws an exception
 *
 * @param string $attribute
 * @param mixed  $value
 * @param string $compareType
 * @param bool   $withTrash
 * @return Model
 */
public function getByOrFail(string $attribute, $value, string $compareType = '=', bool $withTrash = false): Model
```

Examples:

```php
$this->userRepository->getByOrFail('id', 1);

$this->userRepository->getByOrFail('email', '%@gmail.com', 'LIKE');
```

### Get Single Record By Params

To get a single record matching multiple attributes you can use the `getByParams` method:

```php
/**
 * Gets a model by some given attributes
 *
 * @param array  $params
 * @param string $compareType
 * @param bool   $withTrash
 * @return Model|null
 */
public function getByParams(array $params, string $compareType = '=', bool $withTrash = false): ?Model
```

Examples:

```php
$this->userRepository->getByParams([
    ['is_active', true],
    ['email', '%@gmail.com', 'LIKE']
])
```

### Get Single Record By Params Or Fail

To get a single record matching multiple attributes or throw an exception if no record is found
you can use the `getByParamsOrFail` method:

```php
/**
 * Gets a model by some attributes or throws an exception
 *
 * @param array  $params
 * @param string $compareType
 * @param bool   $withTrash
 * @return Model
 */
public function getByParamsOrFail(array $params, string $compareType = '=', bool $withTrash = false): Model
```

Examples:

```php
$this->userRepository->getByParamsOrFail([
    ['is_active', true],
    ['email', '%@gmail.com', 'LIKE']
])
```

### Get All Records By Params

To get all the records matching multiple attributes you can use the `getAllByParams` method:

```php
/**
 * Gets all models by some given attributes
 *
 * @param array  $params
 * @param string $compareType
 * @param bool   $withTrash
 * @return Collection
 */
public function getAllByParams(array $params, string $compareType = '=', bool $withTrash = false): Collection
```

Examples:

```php
$this->userRepository->getAllByParams([
    ['is_active', true],
    ['email', '%@gmail.com', 'LIKE']
])
```

### Update Records By Attribute

To update one or more records you can use the `updateBy` method:

```php
/**
 * Updates one or more models
 * 
 * @param string $attribute
 * @param        $value
 * @param array  $updateFields
 * @return int
 */
public function updateBy(string $attribute, $value, array $updateFields): int
```

Examples:

```php
// UPDATE SINGLE RECORD
$this->userRepository->updateBy('id', 1, ['email' => 'me@example.com']);

// UPDATE MULTIPLE RECORDS
$this->userRepository->updateBy('type', ['owner', 'manager'], ['is_active' => true]);
```

### Delete Records By Attribute

To delete one or more records you can use the `deleteBy` method:

```php
/**
 * Deletes one or more models
 * 
 * @param string $attribute
 * @param        $value
 * @return mixed
 */
public function deleteBy(string $attribute, $value)
```

Examples:

```php
// DELETE SINGLE RECORD
$this->userRepository->deleteBy('id', 1);

// DELETE MULTIPLE RECORDS
$this->userRepository->deleteBy('type', ['owner', 'manager']);
```

### Create New Record

To create a new record you can use the `create` method:

```php
/**
 * Creates a new model
 *
 * @param array $args
 * @return Builder|Model
 */
public function create(array $args)
```

Examples:

```php
$this->userRepository->create([
    'name' => 'John Dee',
    'email' => 'john@example.com',
    'is_active' => true,
])
```

### Disable Global Scope

If your model has a Global Scope and you need to disable it for any queries you can use the
`disableGlobalScope` method:

```php
/**
 * Disables a named global scope
 *
 * @param string $scopeName
 * @return BaseRepository
 */
public function disableGlobalScope(string $scopeName): BaseRepository
```

Examples:

```php
$this->userRepository->disableGlobalScope('active-users');
```

### Enable Glogal Scope

If your model has a disabled Global Scope and you need to enable it again, you can use the
`enableGlobalScope` method:

```php
/**
 * Enables a named global scope
 *
 * @param string $scopeName
 * @return BaseRepository
 */
public function enableGlobalScope(string $scopeName): BaseRepository
```

Examples:

```php
$this->userRepository->enableGlobalScope('active-users');
```

### Protected Helper Methods

Besides all public methods above the `BaseRepository` also provides the following protected
methods that you can use in your Repositories classes:

#### Get Table Name

If you need to get the table name for the model binded to the repository you can use the
`getTable` method:

```php
/**
 * Gets the table for the base model of the repository
 *
 * @return string
 */
protected function getTable(): string
```

Examples:

```php
$usersTable = $this->userRepository->getTable();
```

#### New Query Util

To create new queries you can use the `newQuery` helper method:

```php
/**
 * Builds a new query
 *
 * @param array|string[]|string $columns
 * @return Builder
 */
protected function newQuery(...$columns): Builder
```

Examples:

```php
$this->userRepository->newQuery('id', 'email')
    ->where('is_active', true)
    ->get();
```

## TO DO

- Create command to generate Repositories
- Create tests

## Credits

- [Wendell Adriel](https://github.com/WendellAdriel)
- [All Contributors](../../contributors)

## Contributing

All PRs are welcome.

For major changes, please open an issue first describing what you want to add/change.
