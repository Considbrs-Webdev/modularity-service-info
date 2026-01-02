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
  - General settings (archive page, URL slug, LiteSpeed ESI support)

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

### Using the Module
1. Edit a page with Modularity
2. Add the "Service Information" module
3. Configure display settings
4. Save and publish

## License

MIT License - see [LICENSE](LICENSE) for details.

## Author

[Consid Borås AB](https://github.com/considbrs-webdev)
