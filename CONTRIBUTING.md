# Contributing

If you wish to contribute to iSAIS, feel free to fork the repository and submit
a pull request.

## Coding conventions

### PHP 5.3

* No Class constant visibility modifiers: since PHP 7.1.0
* No method type declarations.
  * Support for object type declarations: since PHP 7.2.0.

### File formatting

#### PHP

Please see <https://www.mediawiki.org/wiki/Manual:Coding_conventions/PHP> for
the general coding convention for PHP files.

#### Indentation

For CSS, JavaScript, JSON, PHP and TOML files, lines should be indented with
1 tab character per indenting level.

For Python files, lines should be indented with 4 whitespace characters
per indenting level.

For YAML files, lines should be indented with 2 whitespace characters per
indenting level.

#### Newlines

* All files should use Unix-style newlines (single LF character, not a CR+LF
  combination).
* All files should have a newline at the end.

#### Encoding

All text files must be encoded with UTF-8 without a
[Byte Order Mark](https://en.wikipedia.org/wiki/Byte_order_mark).

Do not use Microsoft Notepad to edit files, as it always inserts a BOM.

#### Trailing whitespace

Developers should avoid adding trailing whitespace.

#### Line width

Lines should be broken with a line break at maximum 80 characters.

### Naming conventions

Naming cases:

* `snake_case`
* `camelCase`
* `PascalCase`
* `UPPER_CASE`

#### Python

| Name Type                 | Case         |
| -------------------       | ------------ |
| PHP entrypoint file names | `snake_case` |
| PHP other file names      | `PascalCase` |
| const                     | `UPPER_CASE` |
| class                     | `PascalCase` |
| function                  | `camelCase`  |
| method                    | `camelCase`  |
| variable                  | `snake_case` |
| attribute                 | `snake_case` |
| argument                  | `snake_case` |

#### Database

* ALWAYS and ONLY capitalize SQL reserved words in SQL queries.
  * See the official documentations of SQL and the
  [complete list on English Wikipedia](https://en.wikipedia.org/wiki/List_of_SQL_reserved_words)
  as references.
* ALWAYS use `snake_case` for database, table, column, trigger names.
  * Table names and column names may NOT be case-sensitive in SQLite.
  * Database, table, and trigger names may NOT be case-sensitive in
  MySQL/MariaDB.
* Column names should be unique, i.e., same column name should not exist in
  different tables.
* Column names should be prefixed with table names or abbrieviations.
  * For example, `user_id` in `user`, `ug_user` in `user_groups`.

Examples:

```sql
INSERT INTO user ( user_id ) VALUE ( 6856 )
```

## Documentation of external packages

### Development dependencies
