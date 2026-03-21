# oliup/code-generator

A fluent PHP 8.1+ code-generation library. Build PHP source files programmatically using a clean, chainable API — `PHPPrinter` renders every node to a string.

## Requirements

- PHP >= 8.1

## Installation

```sh
composer require oliup/code-generator
```

## Quick start

```php
use OLIUP\CG\PHPFile;
use OLIUP\CG\PHPNamespace;

$file = new PHPFile();
$ns   = new PHPNamespace('App\Models');
$file->addChild($ns);

$class = $ns->newClass('User');
$class->final()->implements(new \OLIUP\CG\PHPInterface('App\Contracts\Identifiable'));

$ctor = $class->newMethod('__construct');
$ctor->newArgument('id')->setType('int')->setPromoted(true)->public();
$ctor->newArgument('name')->setType('string')->setPromoted(true)->public();

$class->newMethod('getId')
      ->public()
      ->setReturnType('int')
      ->addChild('return $this->id;');

echo $file;
```

Output:

```php
<?php
declare(strict_types=1);

namespace App\Models;

final class User implements \App\Contracts\Identifiable
{
    public function __construct(public int $id, public string $name)
    {
    }

    public function getId(): int
    {
        return $this->id;
    }
}
```

## Examples

### Class with PHP 8 attributes

```php
$class = $ns->newClass('UserController');
$class->newAttribute('Route\Controller');
$class->newAttribute('Authorize');

$method = $class->newMethod('show')->public()->setReturnType('mixed');
$method->newAttribute("Get('/users/{id}')");
$method->newArgument('id')->setType('int')->addAttribute('MapEntity');
$method->addChild('return null;');
```

### Union and nullable types

```php
use OLIUP\CG\PHPType;

// int|string
$method->setReturnType(new PHPType('int', 'string'));

// ?array (nullable shorthand)
$method->setReturnType((new PHPType('array'))->nullable());

// A&B intersection
$arg->setType(PHPType::intersection(new PHPClass('Countable'), new PHPClass('Traversable')));
```

### Generic-annotated types

PHP does not support generics at runtime, so generic annotations are stripped from argument and return type declarations by default. Pass `with_generics => true` when printing PHPDoc contexts (e.g. `@method` tags).

```php
$method->setReturnType('array<string,mixed>');
echo $method->getReturnType(); // array
```

### Closure with `use` captures

```php
use OLIUP\CG\PHPFunction;
use OLIUP\CG\PHPVar;

$fn = new PHPFunction();  // anonymous
$fn->newArgument('input')->setType('string');
$fn->setReturnType('string');
$fn->addUse(new PHPVar('prefix'));
$fn->addChild('return $prefix . strtoupper($input);');
```

### Comments and docblocks

```php
use OLIUP\CG\PHPComment;

$class->setComment(PHPComment::doc("Class User.\n\n@package App\Models"));
$method->setComment(PHPComment::inline('Returns the user ID.'));
```

### Trait use with conflict resolution

```php
$useTrait = $class->use(new PHPTrait('LoggableTrait'));
$useTrait->addRule(
    $useTrait->newRule($someMethod)
        ->insteadOf(new PHPTrait('OtherTrait'))
);
```

## Architecture

```
PHPFile -> PHPNamespace -> PHPClass | PHPInterface | PHPTrait | PHPEnum | PHPFunction
PHPClass  -> constants[] + properties[] + methods[] + used_traits[] + implements[]
PHPMethod -> arguments[] + children[] (body as PHPRaw lines)
```

| Class | Purpose |
|---|---|
| `PHPFile` | Root node; emits `<?php` header and `declare(strict_types=1)` |
| `PHPNamespace` | Namespace block; auto-sets namespace on child classes |
| `PHPClass` | Named or anonymous class |
| `PHPInterface` | Interface with method declarations |
| `PHPTrait` | Trait |
| `PHPFunction` | Named or anonymous function / closure |
| `PHPMethod` | Class/interface/trait method |
| `PHPArgument` | Function/method argument (supports promotion, variadic, by-ref) |
| `PHPProperty` | Class property |
| `PHPConstant` | Class or interface constant |
| `PHPType` | Union type; `PHPType::intersection()` for `A&B` |
| `PHPAttribute` | PHP 8 attribute `#[Name(args)]` |
| `PHPComment` | DOC / multiline / hash / slash comment |
| `PHPValue` | Wraps a PHP value; renders via `var_export()` |
| `PHPRaw` | Passthrough for raw PHP source lines |
| `PHPPrinter` | Sole rendering engine |

Every node implements `__toString()` — cast to `(string)` or `echo` to get PHP source.

## `PHPPrinter` options

`printType`, `printMethod`, `printArgument`, `printVar`, and `printNamespace` accept an `array $options` second argument:

| Method | Option | Default | Effect |
|---|---|---|---|
| `printArgument` | `allow_promoted` | `false` | emit visibility prefix for promoted constructor args |
| `printArgument` | `allow_reference` | `true` | emit `&` for by-reference args |
| `printArgument` | `allow_attributes` | `true` | emit `#[...]`; set `false` inside virtual `@method` tags |
| `printMethod` | `declaration` | `false` | emit `;` instead of a body (interface/abstract stubs) |
| `printMethod` | `virtual` | `false` | emit a `@method` PHPDoc tag instead of real PHP |
| `printType` | `with_generics` | `false` | keep `array<K,V>` annotations; stripped by default |
| `printVar` | `standalone` | `true` | emit ` = value;` trailer; set `false` for `use (...)` captures |
| `printNamespace` | `scoped` | `false` | wrap body in `{ }` instead of `;` |

## Development

```sh
make test   # run full test suite
make lint   # static analysis (psalm, error level 4)
make cs     # check code style (phpcs)
make fix    # lint + auto-fix code style

UPDATE_SNAPSHOTS=1 make test  # regenerate snapshot files
```

## License

MIT — see [LICENSE](LICENSE).
