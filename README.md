The plugin copies uploaded media attachments to a remote SFTP connection, removes them locally and updates the uploads image URL option to the new CDN link.

Configuration is set in `wp-config.php`, for example:
```php
// the SFTP server hostname
define ('WP_SFTP_HOST', 'ip or hostname ftp/hosting');
// the SFTP server port (of type int), default is 22
define ('WP_SFTP_PORT', '21');
// SFTP connection user 
define ('WP_SFTP_USER', '');
// SFTP connection password 
define ('WP_SFTP_PASSWORD', '');
// Domain or subdomain to the root of the destination uploads folder
define ('WP_SFTP_CDN_URL', 'subdomain.domain.com');
// Location path for the destionation uploads folder
define ('WP_SFTP_CDN_PATH', '/');
```