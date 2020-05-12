# Laravel Typescript Codegen

[![StyleCI](https://github.styleci.io/repos/257990080/shield?branch=master)](https://styleci.io/repos/257990080)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/jpnut/laravel-typescript-codegen/run-tests?style=flat-square)
![License](https://img.shields.io/github/license/jpnut/laravel-typescript-codegen?style=flat-square)

This package allows you to generate a typescript schema by getting type information from your Laravel routes and controllers.

For example, you can turn this:

```php
    // routes/web.php

    ...

    Route::delete('/foo/{foo}', FooController::class.'@delete')->name('foo.delete');

    ...
```

```php
    // App\Controllers\Web\FooController

    ...

    /**
     * @code-gen
     */
    public function delete(Request $request, Foo $foo): bool
    {
        return $foo->delete();
    }

    ...
```

into this:

```typescript
export const fooDeleteRequest = (foo: string, options?: RequestInit) => request<bool>(`https://localhost/foo/${foo}`, { method: `DELETE`, ...options });
```

## Installation

You can install the package via composer:

```shell script
composer require jpnut/laravel-typescript-codegen
```

## Usage

To generate the schema, use the generate command with the desired file location as the only argument:

```shell script
php artisan code-gen:generate schema.ts
```

In order to generate a function for a given route, you must add the `@code-gen` tag to the doc-block of the controller method which is used by the route.

```php
...

/**
 * @code-gen
 */
public function delete(Request $request, Foo $foo): bool

...
```

This will attempt to get return type information from the doc-block `@return` tag, or, if that is not present, the return type of the method. If there is no type information present, the return type defaults to `any`.

Scalar types (e.g. `string`, `int`, etc.) will be transformed into the appropriate typescript scalar (e.g. `string`, `number`, etc.). If the method returns a class, then the class will be transformed into an interface containing all the public properties of the class.

For example, given the following class:

```php
// App\Objects\User

...

class User
{
    public string $name;

    public int $age;

    /**
     * App\Object\Token[]
     */
    public array $tokens;
}
```

the generated interfaces will look as follows:

```typescript
interface User {
  name: string;
  age: number;
  tokens: Token[];
}

interface Token {
  ...
}
```

### Working with Requests

Generating types for request properties (e.g. the request body) is slightly more involved. The request must implement `JPNut\CodeGen\Contracts\CodeGenRequest`, and should be resolvable by Laravel's Service Container (typically this means extending `Illuminate\Foundation\Http\FormRequest`). You should then create and tag methods which create a mapping from the request to the desired type. For example:

```php
// App\Requests\UpdateUserRequest

...

class UpdateUserRequest extends FormRequest
{
    /**
     * @code-gen-property body
     */
    public function data(): UpdateUserParams
    {
        return new UpdateUserParams([
            'name' => $this->input('name'),
            'age' => $this->input('age'),
        ]);
    }
}
```

```php
// App\Objects\UpdateUserParams

...

class UpdateUserParams
{
    ...

    public ?string $name;

    public ?int $age;
}
```

In this case the generated interfaces and methods would look similar to the following

```typescript
interface UpdateUserRequest {
  body: UpdateUserParams;
}

interface UpdateUserParams {
  name?: string | null;
  age?: number | null;
}

export const fooUpdateRequest = ({ body }: UpdateUserRequest, foo: string, options?: RequestInit) => request<any>(`http://localhost/foo/${foo}`, { method: 'PUT', body: JSON.stringify(body), ...options });
```

Note that the body parameter is serialised for the request automatically. You can change this or serialise other properties by modifying the `request_properties` config property.

### Ignoring Properties

You may have public properties which you do not wish to generate types for. Properties can be ignored by including the `@code-gen-ignore` tag:

```php
class Foo {
    /**
     * @code-gen-ignore
     */
    public string $ignored_property;
}
```

### Using a custom stub

By default, this package will generate the schema using a stub. This stub contains a `request` method which is a wrapper for the fetch api and returns a promise containing the deserialised response data.

If you wish to customise this stub, you should first publish the config file

```shell script
php artisan vendor:publish --provider="JPNut\CodeGen\CodeGenServiceProvider" --tag="config"
```

This will also create a copy of the default stub file located at `resource_path('stubs/typescript-codegen-schema.stub')`. This file will then be used to generate the schema.

### Using a custom interface/method writer

You may wish to change the generated output for interfaces and methods. For example, you might decide to rename the `request` method within the stub. In this case, you should create your own Interface or Method writer and replace the included writer(s) with your version in the `writers` array of the config. The writer(s) should implement the `JPNut\CodeGen\Contracts\InterfaceWriterContract` and `JPNut\CodeGen\Contracts\MethodWriterContract` interfaces respectively.

### Override class resolution

You may wish to resolve certain classes into literals. This is particularly useful for classes which are not part of your codebase. To achieve this, include the class in the `default_literals` array of the config. The class name should be the key, and the value should be an array of php literal types (e.g. string, int etc.)

For example, by default this package converts the `Carbon\Carbon` class into a string. This is necessary since the return type of the `jsonSerialize` method of the class is `string|array`.

## Testing

```shell script
vendor/bin/phpunit
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
