# LiteVideo WordPress Plugin

## Description
LiteVideo is a WordPress plugin that optimizes video performance by converting uploaded videos to WebM format with VP9 codec. It includes batch conversion for existing videos, localization support, and a settings page with FFmpeg error notifications.

## Features
- Converts uploaded videos to WebM (VP9 codec) for faster loading and Google Insights compatibility.
- Batch conversion for existing media library videos.
- Localization-ready with `litevideo` text domain.
- Settings page to enable/disable conversion and trigger batch conversion.
- Displays FFmpeg error on settings page and admin notices if FFmpeg is missing.
- Settings link in the plugins list.

## Requirements
- WordPress 5.0+
- FFmpeg installed on the server
- PHP 7.4+
- Write permissions for file operations

## Installation
1. Download the plugin and unzip it.
2. Upload the `litevideo` folder to `wp-content/plugins/`.
3. Activate the plugin via the WordPress Plugins menu.
4. Ensure FFmpeg is installed on your server.
5. (Optional) Place translation files (`.po`/`.mo`) in `wp-content/plugins/litevideo/languages`.

## Configuration
1. Go to **Settings > LiteVideo** in the WordPress admin.
2. Enable/disable video conversion.
3. Use the "Convert all existing videos to WebM" link for batch conversion.
4. Check for FFmpeg error messages if conversion fails.

## Usage
- Upload videos via the WordPress Media Library; they will automatically convert to WebM if enabled.
- Use the batch conversion link to process existing videos.
- Add translations in the `languages` folder (e.g., `litevideo-en_US.mo`).

## Folder Structure
litevideo/
├── litevideo.php        # Main plugin file
└── languages/           # Translation files (.po/.mo)

## Localization
- Text domain: `litevideo`
- Place `.po`/`.mo` files in the `languages` folder.
- Use tools like Poedit to create translations.

## Troubleshooting
- **FFmpeg Error**: If "FFmpeg is not installed" appears, install FFmpeg on your server.
- **Conversion Fails**: Ensure the server allows `exec` and has write permissions.
- **Translations Not Loading**: Verify `.mo` files are in the `languages` folder and match the site's language.

## License
GPLv2 or later

## Author
Oleksandr Gryshko - https://github.com/Sanetchek

## Contributing
Submit pull requests or issues via the repository.