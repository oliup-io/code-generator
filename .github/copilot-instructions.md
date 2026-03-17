# Copilot Instructions - oliup/code-generator

> IMPORTANT: no hallucination or invention. Go through the entire code base to understand before generating code, the `.github/copilot-instructions.md` or docs. Focus on what can be directly observed in the codebase, not on idealized practices or assumptions.
> When a bug or issue is found in the codebase, do not fix it directly, but rather ask for feedback and approval.
> If `AGENTS.md`, `CLAUDE.md`, `GEMINI.md` do not exist, symlink them to `.github/copilot-instructions.md`.

## Project Overview

A fluent PHP 8.1+ code-generation library. Namespace root: `OLIUP\CG\`. Every node implements `__toString()` via `CommonTrait`, which internally calls `(new PHPPrinter())->print($this)`.

## Architecture

```
PHPFile -> PHPNamespace -> PHPClass | PHPInterface | PHPTrait | PHPEnum | PHPFunction
PHPClass  -> constants[] + properties[] + methods[] + used_traits[] + implements[]
PHPMethod -> arguments[] + children[] (body as PHPRaw lines)
```

- **`PHPPrinter`** is the sole rendering engine; use `$printer->print($node)` or cast `(string) $node`.
- **`PHPRaw`**: passthrough for raw PHP source - `addChild('return $x;')` auto-wraps strings.
- **`PHPValue`**: wraps any PHP value and renders via `var_export()`; anonymous `PHPClass` renders as `new class { ... }`.
- **`PHPType`**: union types via constructor varargs; `PHPType::intersection(...)` for `A&B`; `->nullable()` adds `null`.
- **`PHPAttribute`**: represents a single PHP 8 attribute `#[Name(arg1, arg2)]`; construct with `new PHPAttribute('Name', 'arg1')` or use `addArgument()` fluently.

## Trait Composition

Functionality is assembled from focused traits in `src/Traits/`. Key ones:

- `QualifiedNameAwareTrait` - parses `Foo\Bar\Baz` into namespace + short name; used by class/interface/trait/function.
- `ChildrenAwareTrait` - `addChild()` in `PHPClass`/`PHPInterface`/`PHPTrait` dispatches by type to the right collection.
- `VisibilityAwareTrait` - adds `->public()`, `->protected()`, `->private()` fluent shortcuts.
- `AttributeAwareTrait` - adds `->addAttribute()`, `->getAttributes()`, `->newAttribute()` to `PHPClass`, `PHPInterface`, `PHPTrait`, `PHPMethod`, `PHPProperty`, `PHPConstant`, `PHPFunction`, `PHPArgument`.
- `CommonTrait` - provides `__toString()` and deep-clone `__clone()` for every class.

## Fluent API Pattern

```php
$file = new PHPFile();
$ns   = new PHPNamespace('App\Models');
$file->addChild($ns);

$class = $ns->newClass('User');          // creates, registers, returns
$class->extends('App\Base\Model')->final();

$prop = $class->newProperty('id');
$prop->setType('int')->public();

$ctor = $class->newMethod('__construct');
$arg  = $ctor->newArgument('id');
$arg->setType('int')->setPromoted(true)->public();  // constructor promotion

$class->newMethod('getId')
      ->public()
      ->setReturnType('int')
      ->addChild('return $this->id;');

// PHP 8 attributes
$class->newAttribute('ORM\Entity');                        // #[ORM\Entity]
$class->newAttribute('ORM\Table', "name: 'users'");        // #[ORM\Table(name: 'users')]
$class->newMethod('save')->newAttribute('Override');        // on method
$class->newProperty('slug')->newAttribute('ORM\Column');   // on property
$ctor->newArgument('email')->addAttribute('Assert\Email'); // inline on argument

echo $file;  // full PHP source
```

`new*(string $name)` factory methods (e.g., `newMethod`, `newProperty`, `newArgument`) always create, register in the parent, and return the new object.

## Enums

- `VisibilityEnum` - `PUBLIC | PRIVATE | PROTECTED`
- `CommentKindEnum` - `DOC (/**)` | `MULTILINE (/*)` | `HASH (#)` | `SLASH (//)`; static factories: `PHPComment::doc()`, `::inline()`, `::hash()`, `::multiline()`.

## PHPPrinter API

`PHPPrinter` is the sole rendering engine. Use `$printer->print($node)` for any node, or rely on `(string) $node` which calls it internally.

Print methods that accept behavioural flags use `array $options = []` instead of positional booleans:

| Method | Options key | Default | Effect |
|--------|-------------|---------|--------|
| `printArgument` | `allow_promoted` | `false` | emit visibility prefix for promoted ctor args |
| `printArgument` | `allow_reference` | `true` | emit `&` prefix for by-reference args |
| `printArgument` | `allow_attributes` | `true` | emit `#[...]` on the argument; set to `false` inside virtual `@method` docblocks to avoid invalid PHPDoc |
| `printMethod` | `declaration` | `false` | emit `;` instead of a body (interface/abstract stubs) |
| `printMethod` | `virtual` | `false` | emit a `@method` PHPDoc tag instead of real PHP |
| `printVar` | `standalone` | `true` | emit ` = value;` trailer (set to `false` for `use (...)` captures) |
| `printNamespace` | `scoped` | `false` | wrap body in `{ }` instead of using `;` |

Example:

```php
$printer->printMethod($m, ['declaration' => true]);   // abstract stub
$printer->printMethod($m, ['virtual' => true]);        // @method tag
$printer->printArgument($arg, ['allow_promoted' => true]);
```

## PHPNamespace Side-Effect

`PHPNamespace::validateChild()` automatically calls `$child->setNamespace($this)` for any child that uses `NamespaceAwareTrait`. Adding a class to a namespace sets its namespace automatically.

## Known Limitation

`PHPPrinter::printEnum()` currently returns `''` - enum printing is not yet implemented.

## Developer Workflow

```sh
./csfix           # runs psalm --no-cache then oliup-cs fix (linting + formatting)
./vendor/bin/psalm --no-cache   # static analysis only (error level 4)
./vendor/bin/oliup-cs fix       # code style fix only
./vendor/bin/phpunit --testdox  # run all tests
UPDATE_SNAPSHOTS=1 ./vendor/bin/phpunit  # regenerate snapshot files
```

## Test Structure

Tests live in `tests/` and use PHPUnit 9 with a custom `TestCase` base class.

- `tests/TestCase.php` - abstract base class extending `PHPUnit\Framework\TestCase`; provides `assertEq`, `assertHasStr`, `assertNotHasStr`, `assertThrows`, `assertSnapshot`; all take a description string as the first argument.
- One test class per source class (e.g. `PHPClassTest`, `PHPMethodTest`, etc.).
- `tests/SnapshotTest.php` - full-file PHP output compared against stored snapshots in `tests/snapshots/`.

`TestCase::assertSnapshot($file, $actual)` writes the snapshot on first run and compares on subsequent runs (`UPDATE_SNAPSHOTS=1` to regenerate).

## Code Style Rules

- PHP 8.1+ syntax; strict types always on.
- Every `.php` file gets the OLIUP copyright PHPDoc header (enforced by `php-cs-fixer`).
- **No Unicode shortcut characters in comments or docblocks.** Always use plain ASCII equivalents:

| use      | don't use   |
| -------- | ----------- |
| `->`     | `→`         |
| `<-`     | `←`         |
| `<->`    | `↔`         |
| `-->`    | `───▶`      |
| `>=`     | `≥`         |
| `<=`     | `≤`         |
| `!=`     | `≠`         |
| `*`      | `×`         |
| `/`      | `÷`         |
| `-`      | ` —` or `–` |
| `IN`     | `∈`         |
| `NOT IN` | `∉`         |
| `...`    | `…`         |

- Comments should be concise and human - avoid verbose or redundant prose.
