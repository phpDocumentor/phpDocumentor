# JSON-path

JSON-path is a simple JSON path parser and evaluator for PHP. It is based on the JSONPath 
implementation in [Goessner's JSONPath](http://goessner.net/articles/JsonPath/). 
The code allows you to parse json paths and evaluate them on php objects. Which makes it a query language for 
php object structures.

It's propably not the fastest solution to query php objects, but as the paths are stored as plain strings, it's
easy to use them in configuration files or databases. This makes is a good solution for tools that need to query 
a php object structure based on user input.

## Installation

The recommended way to install JSON-path is through [Composer](http://getcomposer.org).

```bash
  composer require phpdocumentor/json-path
```

## Usage

```php  

$parser = \phpDocumentor\JsonPath\Parser::createInstance();
$query = $parser->parse('.store.book[*].author');

$executor = new \phpDocumentor\JsonPath\Executor();
foreach ($executor->execute($query, $json) as $result) {
    var_dump($result);
}

```
