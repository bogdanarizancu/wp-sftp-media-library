<?php

namespace WPSFTPMediaLibrary;

use phpseclib3\Net\SFTP;

class Plugin
{

	private array $settings;

	private SFTP $connection;

	/**
	 * Main controller transfering files to remote and removing locally.
	 */
	public function uploadRemoteRemoveLocally($metadata)
	{
		$this->loadSettings();

		$this->connectRemotely();

		$this->removeFilesLocally(
			$this->tranferFilesRemote(
				$this->settings['base'],
				$this->settings['path'],
				[]
			)
		);

		return $metadata;
	}

	/**
	 * Transfer files recursevely from current upload folder to remote location.
	 */
	private function tranferFilesRemote($srcDir, $destinationDir, $created): array
	{
		$d = dir($srcDir);
		while ($file = $d->read()) {
			// prevent an infinite loop
			if ($file != "." && $file != "..") {
				if (is_dir($srcDir . '/' . $file)) { // do the following if it is a directory
					if (!$this->connection->chdir($destinationDir . '/' . $file)) {
						$this->connection->mkdir($destinationDir . '/' . $file); // create directories that do not yet exist
					}
					$created = $this->tranferFilesRemote($srcDir . '/' . $file, $destinationDir . '/' . $file, $created);
				} else {
					$upload = $this->connection->put($destinationDir . '/' . $file, $srcDir . '/' . $file, SFTP::SOURCE_LOCAL_FILE); // put the files
					if ($upload) {
						$created[] = $srcDir . '/' . $file;
					}
				}
			}
		}
		$d->close();
		return $created;
	}

	/**
	 * Delete all successfully copied files from local machine.
	 */
	private function removeFilesLocally(array $files): void
	{
		foreach ($files as $file) {
			unlink($file);
		}
	}

	/**
	 * Load relevant settings from wp-config.
	 */
	private function loadSettings()
	{
		$this->settings = [
			'host' => WP_SFTP_HOST ?? '',
			'port' => WP_SFTP_PORT ?? 22,
			'user' => WP_SFTP_USER ?? '',
			'password' => WP_SFTP_PASSWORD ?? '',
			'cdn' => WP_SFTP_CDN_URL ?? '',
			'path' => WP_SFTP_CDN_PATH ?? '/',
			'base' => wp_upload_dir()['basedir']
		];

		// Update uploads url to match remote server.
		if (empty(get_option('upload_url_path'))) {
			update_option('upload_url_path', esc_url($this->settings['cdn']));
		}
	}

	/**
	 * Create SFTP connection, die iif unsuccessful.
	 */
	private function connectRemotely(): SFTP
	{
		$this->connection = new SFTP($this->settings['host'], $this->settings['port']);
		if (!$this->connection->login($this->settings['user'], $this->settings['password'])) {
			die('Connection attempt failed, Check your settings');
		}
		return $this->connection;
	}
}