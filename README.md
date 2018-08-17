# Compressed Int Set

Collects a Set of `Int`eger values such that contiguous `Int`egers that are in the `Set`
are collapsed into a range definition. For example the following `Set` of integers:

```php
<?php
$array = [1, 2, 3, 4, 11, 12, 13, 14, 15, 22, 23, 24, 25, 26, 200       ];
//       |___________|___________________|___________________|__________|
```

Is internally represented by this data structure like follows:

```php
<?php
$array = [1 => 4,     11 => 15,           22 => 26,           200 => 200];
```

Main goal of this data structure is to reduce memory usage.

`contains()` requires linear search `O(n)` - but this should be implemented with a search
tree and have `O(log n)` search. This is currently not the case for various reasons.

The class should not be used as-is but wrapped in another that encapsulates the types
better. It does not bundle an iterator, which may be implemented in said wrapper class
if needed. It may look like this:

```php
<?php

use PHPToolBucket\CompressedIntSet\CompressedIntSet;

function makeIterator(CompressedIntSet $set): Iterator{
    foreach($set->ranges as $start => $end){
        do{
            yield $start++;
        }while($start <= $end);
    }
}
```

## Installation

```
composer require php-tool-bucket/compressed-int-set
```
