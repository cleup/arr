# Cleup - Working with arrays

#### Installation

Install the `cleup/arr` library using composer:

```
composer require cleup/arr
```

#### Usage

##### Dot syntax methods
```php 
use Cleup\Helpers\Arr;

// Initial array
$array = array(
    'name'  => 'Edward',
    'data' => [
        'age' => 21,
        'gender' => 'male'
    ]
);

# Set - Sets the value by overwriting all items
Arr::set('name', 'Jimmy', $array);
Arr::set('data.age', 18, $array);
Arr::set('data.tasks', [
    'Read the book',
    'Go to a restaurant'
], $array);
/*
[
  "name" => "Jimmy",
  "data" => [
    "age" => 18,
    "gender" => "male",
    "tasks" => [
      0 => "Read the book",
      1 => "Go to a restaurant"
    ]
  ]
]
*/

# Replace - Replaces the old value with the new one without overwriting the whole array or its fragment
Arr::replace('data', [
    'age' => 30,
], $array);
/*
...
  "data" => [
    "age" => 30,
    ...
...
*/

# Push - Adds a new element to the end of the array
Arr::push('data.tasks', 'Feeding the cat', $array);
/*
...
    "tasks" => [
      0 => "Read the book",
      1 => "Go to a restaurant",
      2 => "Feeding the cat"
    ]
...
*/

# Unshift - Adds a new element to the beginning of the array
Arr::unshift('data.tasks', 'Watch a movie', $array);
/*
...
    "tasks" => [
      0 => "Watch a movie",
      1 => "Read the book",
      2 => "Go to a restaurant",
      3 => "Feeding the cat"
    ]
...
*/

# Delete - Deletes a value from the array
Arr::delete('data.gender', $array);
/*
...
  "data" => [
    "age" => 30,
    "tasks" => [
      0 => "Read the book",
      1 => "Go to a restaurant"
...
*/

# Get - Recursively get the value of the array
Arr::get('name', $array); // Jimmy
Arr::get('data.tasks.0', $array); // Watch a movie
Arr::get('data.tasks', $array);
/*
[
    0 => "Watch a movie",
    1 => "Read the book",
    2 => "Go to a restaurant",
    3 => "Feeding the cat"
]
*/

# Has - Does the array contain the specified key
Arr::has('data.tasks.1', $array); // true
Arr::has('data.work', $array); // false
```

##### Default methods
```php
# isAssoc - Determines if an array is associative.
$assoc = array(
    'name'  => 'Edward',
    'gender' => 'male'
);
$list = ['Apple', 'Orange'];

Arr::isAssoc($assoc); // true
Arr::isAssoc($list);  // false

# isList - Determines whether the array is a list.
Arr::isList($list);  // true
Arr::isList($assoc); // false

# Map - Matching for each of the array elements.
$newAssoc = Arr::map($assoc, function($value, $key) {
    return strtoupper($value);
});
/*
[
    'name'  => 'EDWARD',
    'gender' => 'MALE'
];
*/

# Query - Convert the array into a query string.
$result =  Arr::query($assoc); // "name=Edward&gender=male"

# Divide - Divide the array into keys and values.
[$keys, $values] = Arr::divide($assoc);
/* 
    $keys = [
        0 => 'name',
        1 => 'gender'
    ];
    $values = [
        0 => "Edward"
        1 => "male"
    ];
*/

# Join - Join all items using a string.
Arr::join($list, ','); // "Apple,Orange,Cherry"
Arr::join($list, ', ', ' or '); // "Apple, Orange or Cherry"
```

##### Write array to file
```php
$filePath = __DIR__ . '/data/myArray.php';
$arr = [
    'id' => 1,
    'name' => 'Cleup'
];

Arr::write($arr, $filePath);
// data/myArray.php
return [
    'id' => 1,
    'name' => 'Cleup'
];

// Use a variable and a comment
Arr::write($arr, $filePath, [
    'variable' => 'myArray', // Or leave it blank to save without a variable
    'comment'  => 'This is my array'
]);
// data/myArray.php
/* This is my array */
$myArray = [
    'id' => 1,
    'name' => 'Cleup'
];

// Callback and response
$status = Arr::write($arr, $filePath, [
    'write' => false // Do not write to file
], function($content, $array, $file, &$response) {
    file_put_contents($file, $content);
    sleep(7);
    $response = false;
}); // false
```