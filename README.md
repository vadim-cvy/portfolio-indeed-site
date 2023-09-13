# Boilerplate for WP plugins and themes

---

## Installation

### Theme
1. Remove `theme.` name part from `theme.functions.php` and `theme.style.css` files.
2. Remove `plugin.plugin-name.php`.
3. Update theme details in `style.css`.
4. Follow instruction from "Common" section (below).

### Plugin
1. Rename `plugin.plugin-name.php` file to `{your-plugin-dir-name}.php`.
2. Remove files starting with `theme.`.
3. Update plugin details in `{your-plugin-dir-name}.php`.
4. Follow instruction from "Common" section (below).

### Common
1. Execute `composer install`.
2. Execute `npm install`.

---

## Development

### PHP
1. All includes must be stored at `/inc`.
2. Autoload follows PSR-4 standard. Ex: `\Jlx\DirName\ClassName` = `./inc/DirName/ClassName.php`.
3. See `composer.json` >> `repositories` to view my utility packages which will help in dev process and will allow you to keep the same structure and common code base between different projects.

### JS
1. Prefer TypeScript over common JS.
2. JS is bundled and compiled with WebPack:
    * WebPack will detect entry points automatically - no need to add them manually to config file.
    * Entry points are handled like this: `/assets/js/src/{entry-point-name}/.index.ts` will be compiled into `/assets/js/dist/{entry-point-name}/index{.min}.js`.
3. Compilation
    * Execute `npm run dev` to make webpack watch your changes during dev process (js will be re-compiled each time file is saved). Files generated during dev process will contain `.dev` in their names and must be used in local/staging environment only.
    * Execute `npm run build` to compile production-ready js files. Files generated during build process will contain `.prod` in their names and must be used in production environment.
