# Changelog

The repository carries no release tag, so this file starts at the 1.x development line. What came
before it is its history.

## Unreleased (1.x-dev)

### Breaking changes

- **PHP 8.5 is the minimum** (`"php": ">=8.5"`), and `silassare/php-utils` is `^2.0`.

- **A fluent method returns `static`, not `$this`**, in every class and trait, so a subclass's
  chain keeps its own type. A caller sees no change; a subclass overriding one must match.

### Added

- **The 8.5 syntax, written by the shared coding standard.** The floor moved to PHP 8.5 and the
  fixer carried no version migration set, so each file kept the syntax of the year it was written.
  The standard now applies the 8.5 migration: `$x::class` over `get_class($x)`, `0o755` over
  `0755`, an arrow function over a closure that only returns, a heredoc indented with its code. One file here.

- **Property hooks**: `PHPPropertyHook` is a `get` or a `set` hook in either body form PHP accepts,
  an expression (`=> $expr;`) or a block, with `PHPPropertyHook::setExpression()` and
  `PropertyHookKindEnum::GET` / `SET`; a property carries them through `PHPProperty::addHook()` and
  `getHooks()`, one per kind, and `validate()` refuses a hook with no body or with two. A generator
  can now write a virtual typed property instead of a docblock line and a magic accessor.

- **A constant can be typed**: `PHPConstant` uses `TypeAwareTrait`, so a generated
  `public const string NAME = '...'` is printed with its type, which PHP 8.3 allows and which a
  subclass redeclaring it must then carry too.

- **Attributes on anything that can hold one**: `PHPAttribute` (a name and its arguments) and
  `Traits\AttributeAwareTrait`, used by the class, interface, trait, enum, constant, property,
  method and argument builders. A virtual method's docblock may leave its arguments' attributes out.

- **`PHPPrinter::printType()` can print a generic annotation** (`with_generics`), so a docblock type
  keeps its element type (`array<string, Thing>`) where the PHP type cannot carry it. The printer's
  `printArgument()`, `printMethod()`, `printVar()` and `printNamespace()` take an options array for
  the same reason.

### Fixed

- An anonymous function is refused where it cannot go: adding one straight to a namespace used to
  be accepted and printed invalid PHP (`validateChild()`).
- `printVar()` ends a standalone variable with a semicolon and an inline one without, instead of
  always writing one.
- A lambda is formatted as the rest is.
