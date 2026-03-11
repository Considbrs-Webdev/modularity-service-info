# Modularity Service Information

A WordPress plugin that provides a complete solution for managing and displaying service information. Built as a Modularity module for use with the Municipio theme.

## Features

### Custom Post Type

- **Service Information Post Type** (`service_information`) for creating and managing service info entries
- Supports title, content, thumbnail, excerpt, and revisions
- Configurable URL slug via settings
- REST API support for headless usage

### Taxonomy

- **Service Categories** taxonomy for organizing service information
- Hierarchical categories with custom icons
- Admin column for easy filtering

### Modularity Module

- **Service Info Module** for displaying service information on pages and posts
- Flexible display options:
  - Configurable number of posts to show
  - Option to show/hide icons
  - Link to service information archive
  - Archive mode (show all posts)
  - Group posts by categories
  - Show/hide empty categories
- Custom styling and responsive design

### Single Post Template

- Custom template for displaying individual service information posts
- Displays service date range with formatted dates
- Integrates with Municipio theme components

### Menu Integration

- Custom menu item type for adding service information link to navigation
- **Badge notification** showing the count of active service information posts
- Works with both classic menu editor and Customizer
- LiteSpeed ESI cache support for dynamic badge updates

### Automatic Unpublishing

- Cron job for automatically unpublishing expired service information
- Configurable action on expiration (draft or trash)
- WP-CLI command support: `wp service-info unpublish`
- Supports `--dry-run` and `--limit` options

### ACF Integration

- Settings page under Settings → Service Information
- Custom fields for:
  - Post data (start date, end date, unpublish settings)
  - Module settings (display options)
  - Taxonomy settings (icons per category)
  - General settings (archive page, URL slug, LiteSpeed ESI support, import schedule)

### External Importer System

- Register external data sources to automatically import service information from third-party systems
- Importers run on a configurable cron schedule (hourly or daily), set under **Settings → Service Information**
- Each importer handles its own data fetching and maps it to a `ServiceInfoItem` DTO
- Idempotent upserts: existing posts are matched by source key + source ID and updated, not duplicated
- WP-CLI command: `wp service-info import [--importer=<key>] [--dry-run]`

#### Registering a Custom Importer

Implement `ModularityServiceInfo\Import\ImporterInterface`:

```php
use ModularityServiceInfo\Import\ImporterInterface;
use ModularityServiceInfo\Import\ImporterRegistry;
use ModularityServiceInfo\Import\ServiceInfoItem;

class MyCustomImporter implements ImporterInterface
{
    public function getKey(): string
    {
        return 'my-source'; // slug-like, unique key used for deduplication
    }

    public function getName(): string
    {
        return 'My External Source';
    }

    public function import(): array
    {
        // Fetch data from your source and return ServiceInfoItem instances
        return [
            new ServiceInfoItem(
                sourceId: 'item-123',
                title: 'Water outage in district X',
                content: '<p>Planned maintenance work.</p>',
                startDate: new \DateTimeImmutable('2026-03-15 08:00:00', new \DateTimeZone('UTC')),
                endDate: new \DateTimeImmutable('2026-03-15 17:00:00', new \DateTimeZone('UTC')),
                categories: ['Water'],
                unpublishDate: new \DateTimeImmutable('2026-03-15 18:00:00', new \DateTimeZone('UTC')),
                onUnpublish: 'trash',
                publishDate: null, // null = publish immediately
            ),
        ];
    }
}
```

Hook into `modularity_service_info_register_importers` to register your importer:

```php
add_action('modularity_service_info_register_importers', function (ImporterRegistry $registry) {
    $registry->register(new MyCustomImporter());
});
```

#### `ServiceInfoItem` Fields

| Parameter       | Type                      | Required | Description                                                                        |
| --------------- | ------------------------- | -------- | ---------------------------------------------------------------------------------- |
| `sourceId`      | `string`                  | Yes      | Unique ID from the source system, used for deduplication                           |
| `title`         | `string`                  | Yes      | Post title                                                                         |
| `content`       | `string`                  | Yes      | Post content (HTML allowed)                                                        |
| `startDate`     | `DateTimeImmutable`       | Yes      | When the service information becomes active                                        |
| `endDate`       | `DateTimeImmutable\|null` | No       | When it expires; `null` = no expiry                                                |
| `categories`    | `string[]`                | No       | Taxonomy term names for `service_category`                                         |
| `unpublishDate` | `DateTimeImmutable\|null` | No       | Auto-unpublish date (GMT/UTC); `null` = no auto-unpublish                          |
| `onUnpublish`   | `string`                  | No       | Action when unpublished: `'draft'` (default) or `'trash'`                          |
| `publishDate`   | `DateTimeImmutable\|null` | No       | Post publish date (GMT/UTC); `null` = publish immediately; future date = scheduled |

> **Note:** `unpublishDate` and `publishDate` must be expressed in GMT/UTC.

### Typesense Search Integration

- Integrates with the Municipio Typesense search plugin
- Enriches indexed documents with formatted date range and icon name fields
- Registers a dedicated `service-info` hit template for search results
- Enqueues service info styles on search pages

### Developer Features

- Filters for customizing behavior:
  - `Modularity/ServiceInformation/Posts/Slug` - Customize post type slug
  - `Modularity/ServiceInformation/Module/ArchiveLink/Icon` - Customize archive link icon
- Decorators for extending post meta functionality
- Validation handlers for ACF fields
- Cache busting for assets

## Requirements

- WordPress 6.0+
- [Modularity](https://github.com/helsingborg-stad/Modularity) 3.0+
- [Municipio](https://github.com/helsingborg-stad/Municipio) 6.0+ (recommended)
- Advanced Custom Fields PRO

## Installation

1. Upload the plugin to `/wp-content/plugins/modularity-service-info/`
2. Activate the plugin through the WordPress admin
3. Configure settings under **Settings → Service Information**
4. Add the Service Info module to pages using Modularity

## Configuration

### General Settings

- **Archive Page**: Select a page to use as the service information archive
- **URL Slug**: Customize the permalink structure for service information posts
- **LiteSpeed ESI Cache**: Enable ESI support for menu badge (if running LiteSpeed)
- **Import Schedule**: How often to run registered external importers (`hourly`, `daily`, or disabled)

### Module Settings

Configure display options when adding the module:

- Number of posts to display
- Show/hide category icons
- Enable archive link
- Group by categories

### Menu Badge

1. Go to **Appearance → Menus** or use the Customizer
2. Find "Service Information" in the menu items
3. Add it to your menu
4. The badge will automatically show the count of published service info posts

## Usage

### Creating Service Information

1. Go to **Service Information → Add New**
2. Enter title and content
3. Set the start and end dates
4. Optionally configure automatic unpublishing
5. Assign a service category
6. Publish

### Running Importers via WP-CLI

Run all registered importers:

```bash
wp service-info import
```

Run a specific importer by key:

```bash
wp service-info import --importer=my-source
```

Preview what would be created/updated without writing to the database:

```bash
wp service-info import --dry-run
```

### Using the Module

1. Edit a page with Modularity
2. Add the "Service Information" module
3. Configure display settings
4. Save and publish

## License

MIT License - see [LICENSE](LICENSE) for details.

## Author

[Consid Borås AB](https://github.com/considbrs-webdev)
