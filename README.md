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

# Sets the value by overwriting all items
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

# Replaces the old value with the new one without overwriting the whole array or its fragment
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

# Adds a new element to the end of the array
Arr::append('data.tasks', 'Feeding the cat', $array);
/*
...
    "tasks" => [
      0 => "Read the book",
      1 => "Go to a restaurant",
      2 => "Feeding the cat"
    ]
...
*/

# Adds a new element to the beginning of the array
Arr::prepend('data.tasks', 'Watch a movie', $array);
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

# Deletes a value from the array
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

# Recursively get the value of the array
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

# Does the array contain the specified key
Arr::has('data.tasks.1', $array); // true
Arr::has('data.work', $array); // false
```

##### Default methods
```php
# Determines if an array is associative.
$assoc = array(
    'name'  => 'Edward',
    'gender' => 'male'
);
$list = ['Apple', 'Orange'];

Arr::isAssoc($assoc); // true
Arr::isAssoc($list);  // false

# Determines whether the array is a list.
Arr::isList($list);  // true
Arr::isList($assoc); // false

# Matching for each of the array elements.
$newAssoc = Arr::map($assoc, function($value, $key) {
    return strtoupper($value);
});
/*
[
    'name'  => 'EDWARD',
    'gender' => 'MALE'
];
*/

# Convert the array into a query string.
$result =  Arr::query($assoc); // "name=Edward&gender=male"

# Divide the array into keys and values.
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

# Join all items using a string.
Arr::join($list, ','); // "Apple,Orange,Cherry"
Arr::join($list, ', ', ' or '); // "Apple, Orange or Cherry"

# Retrieve only the required elements from the specified array.
Arr:only($assoc, 'gender') // ['gender' => 'male']
Arr:only($assoc, [
    'name',
    'gender'
]) // ['name' => 'Edward', gender' => 'male']
```