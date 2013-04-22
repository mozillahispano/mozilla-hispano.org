<?php

/**
 * Deactivate plugin after activation error
 *
 * @return void
 */
function w3_activation_cleanup() {
    $active_plugins = (array) get_option('active_plugins');
    $active_plugins_network = (array) get_site_option('active_sitewide_plugins');

    // workaround for WPMU deactivation bug
    remove_action('deactivate_' . W3TC_FILE, 'deactivate_sitewide_plugin');

    do_action('deactivate_plugin', W3TC_FILE);

    $key = array_search(W3TC_FILE, $active_plugins);

    if ($key !== false) {
        array_splice($active_plugins, $key, 1);
    }

    unset($active_plugins_network[W3TC_FILE]);

    do_action('deactivate_' . W3TC_FILE);
    do_action('deactivated_plugin', W3TC_FILE);

    update_option('active_plugins', $active_plugins);
    update_site_option('active_sitewide_plugins', $active_plugins_network);
}

/**
 * W3 activate error
 *
 * @param string $error
 * @return void
 */
function w3_activate_error($error) {
    w3_activation_cleanup();

    include W3TC_INC_DIR . '/error.php';
    exit();
}

/**
 * Print activation error with repeat button based on exception
 *
 * @param $e
 */
function w3_activation_error_on_exception($e) {
    $reactivate_url = wp_nonce_url('plugins.php?action=activate&plugin=' . W3TC_FILE, 'activate-plugin_' . W3TC_FILE);
    $reactivate_button = sprintf('<input type="button" value="re-activate plugin" onclick="top.location.href = \'%s\'" />', addslashes($reactivate_url));

    w3_activate_error(sprintf('%s<br />then %s.', $e->getMessage(), $reactivate_button));
}

/**
 * W3 writable error
 *
 * @param string $path
 * @return string
 */
function w3_writable_error($path) {
    $reactivate_url = wp_nonce_url('plugins.php?action=activate&plugin=' . W3TC_FILE, 'activate-plugin_' . W3TC_FILE);
    $reactivate_button = sprintf('<input type="button" value="re-activate plugin" onclick="top.location.href = \'%s\'" />', addslashes($reactivate_url));

    try {
        w3_throw_on_write_error($path);
    } catch (Exception $e) {
        w3_activation_error_on_exception($e);
    }
}

/**
 * W3 error on read
 *
 * @param string $path
 * @throws FileOperationException
 */
function w3_throw_on_read_error($path) {
    w3_require_once(W3TC_INC_DIR . '/functions/file.php');

    if (w3_check_open_basedir($path)) {
        $error = sprintf('<strong>%s</strong> could not be read, please run following ' .
            'command:<br /><strong style="color: #f00;">chmod 777 %s</strong>', $path,
            (file_exists($path) ? $path : dirname($path)));
    } else {
        $error = sprintf('<strong>%s</strong> could not be read, <strong>open_basedir' .
            '</strong> restriction in effect, please check your php.ini settings:<br />' .
            '<strong style="color: #f00;">open_basedir = "%s"</strong>', $path,
            ini_get('open_basedir'));
    }

    throw new FileOperationException($error, 'read', 'file', $path);
}

/**
 * W3 writable error
 *
 * @param string $path
 * @param string[] $chmod_dirs Directories that should be chmod 777 inorder to write
 * @throws FileOperationException
 */
function w3_throw_on_write_error($path, $chmod_dirs = array()) {
    w3_require_once(W3TC_INC_DIR . '/functions/file.php');
    $chmods = '';
    if ($chmod_dirs) {
        $chmods = '<ul>';
        foreach($chmod_dirs as $dir) {
            $chmods .= sprintf('<li><strong style="color: #f00;">chmod 777 %s</strong></li>', $dir);
        }
    } else {
        $chmods = sprintf('<strong style="color: #f00;">chmod 777 %s</strong>',
                         (file_exists($path) ? $path : dirname($path)));
    }
    if (w3_check_open_basedir($path)) {
        $error = sprintf('<strong>%s</strong> could not be created, please run following ' .
            'command:<br />%s', $path,
            $chmods);
    } else {
        $error = sprintf('<strong>%s</strong> could not be created, <strong>open_basedir' .
            '</strong> restriction in effect, please check your php.ini settings:<br />' .
            '<strong style="color: #f00;">open_basedir = "%s"</strong>', $path,
            ini_get('open_basedir'));
    }

    throw new FileOperationException($error, 'create', 'file', $path);
}

/**
 * Creates cache folder and extension files.
 * Throws exception on failure.
 *
 * @throws Exception
 */
function w3_activation_create_required_files() {
    /**
    * Check installation files
    */
    $files = array(
        W3TC_INSTALL_FILE_ADVANCED_CACHE,
        W3TC_INSTALL_FILE_DB,
        W3TC_INSTALL_FILE_OBJECT_CACHE);

    $nonexistent_files = array();

    foreach ($files as $file) {
        if (!file_exists($file)) {
            $nonexistent_files[] = $file;
        }
    }

    if (count($nonexistent_files)) {
        throw new Exception(sprintf('Unfortunately core file(s): (<strong>%s</strong>) ' .
            'are missing, so activation will fail. Please re-start the installation ' .
            'process from the beginning.', implode(', ', $nonexistent_files)));
    }

    $directories = array(
        W3TC_CACHE_DIR,
        W3TC_CONFIG_DIR,
        W3TC_CACHE_CONFIG_DIR,
        W3TC_CACHE_TMP_DIR);

    try{
        w3_wp_create_folders($directories, 'direct');
    } catch(Exception $ex) {}
}

/**
 * Copy file if destination does not exists or not equal
 * 
 * @param string $source_filename
 * @param string $destination_filename
 * @param string $method Which method to use when creating
 * @throws FilesystemCredentialException with S/FTP form if it can't get the required filesystem credentials
 * @throws FileOperationException
 */
function w3_copy_if_not_equal($source_filename, $destination_filename, $method = 'direct') {
    if (@file_exists($destination_filename)) {
        $s = fopen($source_filename, 'rb');
        if ($s) {
            $v1 = fread($s, filesize($source_filename));
            fclose($s);
            
            $d = fopen($destination_filename, 'rb');
        
            if ($d) {
                if (filesize($destination_filename) <= 0)
                    $v2 = '';
                else
                    $v2 = fread($d, filesize($destination_filename));
                
                fclose($d);
                
                if ($v1 && $v2 && $v1 == $v2)
                    return;
            }
        }
    }

    w3_wp_copy_file($source_filename, $destination_filename, $method);
}


/**
 * Copy file using WordPress filesystem functions.
 * @param $source_filename
 * @param $destination_filename
 * @param string $method Which method to use when creating
 * @param string $url Where to redirect after creation
 * @param bool|string $context folder to copy files too
 * @throws FilesystemCredentialException with S/FTP form if it can't get the required filesystem credentials
 * @throws FileOperationException
 */
function w3_wp_copy_file($source_filename, $destination_filename, $method = '', $url = '', $context = false) {
    $contents = @file_get_contents($source_filename);
    if ($contents) {
        @file_put_contents($destination_filename, $contents);
    }
    if (@file_exists($destination_filename)) {
        if (@file_get_contents($destination_filename) == $contents)
            return;
    }

    w3_wp_request_filesystem_credentials($method, $url, $context);

    global $wp_filesystem;
    $contents = $wp_filesystem->get_contents($source_filename);

    if (!$wp_filesystem->put_contents( $destination_filename, $contents, FS_CHMOD_FILE)) {
        throw new FileOperationException('Could not create file ' . $destination_filename, 'create', 'file', $destination_filename);
    }
}

/**
 * @param $folder
 * @param string $method Which method to use when creating
 * @param string $url Where to redirect after creation
 * @param bool|string $context folder to create folder in
 * @throws FilesystemCredentialException with S/FTP form if it can't get the required filesystem credentials
 * @throws FileOperationException
 */
function w3_wp_create_folder($folder, $method = '', $url = '', $context = false) {
    if (!@is_dir($folder) && !@w3_mkdir($folder)) {
        w3_wp_request_filesystem_credentials($method, $url, $context);

        global $wp_filesystem;
        if (!$wp_filesystem->mkdir($folder, FS_CHMOD_DIR)) {
            throw new FileOperationException('Could not create directory:' . $folder, 'create', 'folder', $folder);
        }
    }
}

/**
 * @param $folders
 * @param string $method Which method to use when creating
 * @param string $url Where to redirect after creation
 * @param bool|string $context path folder where delete folders resides
 * @throws FilesystemCredentialException with S/FTP form if it can't get the required filesystem credentials
 * @throws FileOperationException
 */
function w3_wp_delete_folders($folders, $method = '', $url = '', $context = false) {
    $delete_folders = array();
    foreach($folders as $folder) {
        if (is_dir($folder))
            $delete_folders[] = $folder;
    }
    if (empty($delete_folders))
        return;

    $removed = true;
    foreach ($delete_folders as $folder) {
        w3_rmdir($folder);
        if (@is_dir($folder))
            @rmdir($folder);
        if (@is_dir($folder)) {
            $removed = false;
            break;
        }
    }

    if ($removed)
        return;
    w3_wp_request_filesystem_credentials($method, $url, $context);

    global $wp_filesystem;

    foreach($delete_folders as $folder) {
        w3_rmdir($folder);
        if (file_exists($folder))
            if (!$wp_filesystem->rmdir($folder, FS_CHMOD_DIR)) {
                throw new FileOperationException('Could not delete directory: ' . $folder, 'delete', 'folder', $folder);
            }
    }
}

/**
 * @param $file
 * @param string $method
 * @param string $url
 * @param bool|string $context folder where file to be deleted resides
 * @throws FilesystemCredentialException with S/FTP form if it can't get the required filesystem credentials
 * @throws FileOperationException
 */
function w3_wp_delete_file($file, $method = '', $url = '', $context = false) {
    if (!@unlink($file)) {
        w3_wp_request_filesystem_credentials($method, $url, false, $context = false);

        global $wp_filesystem;

        if (file_exists($file) && !$wp_filesystem->delete($file)) {
            throw new FileOperationException('Could not delete file: ' . $file, 'delete', 'file', $file);
        }
    }
}

/**
 * Get WordPress filesystems credentials. Required for WP filesystem usage.
 * @param string $method Which method to use when creating
 * @param string $url Where to redirect after creation
 * @param bool|string $context path to folder that should be have filesystem credentials. If false WP_CONTENT_DIR is assumed
 * @throws FilesystemCredentialException with S/FTP form if it can't get the required filesystem credentials
 */
function w3_wp_request_filesystem_credentials($method = '', $url = '', $context = false) {
    $success = true;
    ob_start();
    if (false === ($creds = request_filesystem_credentials($url, $method, false, $context, array()))) {
        $success =  false;
    }
    $form = ob_get_contents();
    ob_end_clean();

    ob_start();
    // If first check failed try again and show error message
    if (!WP_Filesystem($creds) && $success) {
        request_filesystem_credentials($url, $method, true, false, array());
        $success =  false;
        $form = ob_get_contents();
    }
    ob_end_clean();

    if (!$success) {
        throw new FilesystemCredentialException('Could not get write credentials', $method, $form);
    }
}

/**
 * Create files
 * @param $files array(from file => to file)
 * @param string $method
 * @param string $url
 * @param bool|string path to folder where files should be created
 * @throws FileOperationException
 */
function w3_wp_create_files($files, $method = '', $url = '', $context = false) {
    if (empty($files))
        return;

    $created = true;
    foreach ($files as $source_filename => $destination_filename) {
        $contents = @file_get_contents($source_filename);
        if ($contents && !@file_put_contents($destination_filename, $contents)) {
            $created = false;
            break;
        }
    }

    if ($created)
        return;

    w3_wp_request_filesystem_credentials($method, $url, $context);

    global $wp_filesystem;

    foreach($files as $source_filename => $destination_filename) {
        $contents = $wp_filesystem->get_contents($source_filename);

        if (file_exists($destination_filename) && ! $wp_filesystem->delete($destination_filename))
            throw new FileOperationException('Could not delete file: ' . $destination_filename, 'delete', 'file', $destination_filename);

        if (!$wp_filesystem->put_contents($destination_filename, $contents, FS_CHMOD_FILE)) {
            throw new FileOperationException('Could not create file: ' . $destination_filename, 'create', 'file', $destination_filename);
        }
    }
}

/**
 * Create folders in wp content
 * @param $folders array(folderpath1, folderpath2, ...)
 * @param string $method
 * @param string $url
 * @param bool|string $context folder to create folders in
 * @throws FilesystemCredentialException with S/FTP form if it can't get the required filesystem credentials
 * @throws FileOperationException
 */
function w3_wp_create_folders($folders, $method = '', $url = '', $context = false) {
    if (empty($folders))
        return;

    $created = true;
    foreach ($folders as $folder) {
        if (!@is_dir($folder) && !@w3_mkdir_from($folder, WP_CONTENT_DIR)) {
            $created = false;
            break;
        }
    }
    if ($created)
        return;

    w3_wp_request_filesystem_credentials($method, $url, $context);

    global $wp_filesystem;

    foreach($folders as $folder) {
        if (!@is_dir($folder) && !$wp_filesystem->mkdir($folder, FS_CHMOD_DIR)) {
            throw new FileOperationException('Could not create directory:' . $folder, 'create', 'folder', $folder);
        }
    }
}

/**
 * Tries to write file content
 * @param string $filename path to file
 * @param string $content data to write
 * @param string $method Which method to use when creating
 * @param string $url Where to redirect after creation
 * @param bool|string $context folder in which to write file
 * @throws FilesystemCredentialException with S/FTP form if it can't get the required filesystem credentials
 * @throws FileOperationException
 * @return bool;
 */
function w3_wp_write_to_file($filename, $content, $method = '', $url = '', $context = false) {
    if (@file_put_contents($filename, $content))
        return true;

    w3_wp_request_filesystem_credentials($method, $url, $context);
    $permissions = array(0644, 0664, 0666);
    global $wp_filesystem;
    if (!$wp_filesystem->put_contents($filename, $content)) {
        foreach ($permissions as $permission) {
            try{
                w3_chmod_file($filename, $permission);
                if ($wp_filesystem->put_contents($filename, $content))
                    return true;
            }catch (Exception $ex) {}
        }
        throw new FileOperationException('Could not write to file: ' . $filename, 'write', 'file', $filename);
    }
    return true;
}

/**
 * Tries to read file content
 * @param string $filename path to file
 * @param string $method Which method to use when creating
 * @param string $url Where to redirect after creation
 * @param bool|string $context folder to read from
 * @return mixed
 * @throws FilesystemCredentialException with S/FTP form if it can't get the required filesystem credentials
 * @throws FileOperationException
 */
function w3_wp_read_from_file($filename, $method = '', $url = '', $context = false) {
    $content = @file_get_contents($filename);
    if ($content)
        return $content;

    w3_wp_request_filesystem_credentials($method, $url, $context);

    global $wp_filesystem;
    if (!($content = $wp_filesystem->get_contents($filename))) {
        throw new FileOperationException('Could not read file: ' . $filename, 'write', 'file', $filename);
    }
    return $content;
}

/**
 * @param string $dir path to dir
 * @param int $permission
 * @param bool $recursive
 * @param string $method
 * @param string $url
 * @param bool|string $context folder where dir resides to chmod
 * @return bool
 * @throws FilesystemCredentialException with S/FTP form if it can't get the required filesystem credentials
 * @throws FileOperationException
 */
function w3_chmod_dir($dir, $permission, $recursive = false, $method = '', $url = '', $context = false) {
    if (!is_dir($dir) || !file_exists($dir))
        return false;

    if (@chmod($dir, $permission))
        return true;

    w3_wp_request_filesystem_credentials($method, $url, $context);

    global $wp_filesystem;

    if (!$wp_filesystem->chmod($dir, $permission, $recursive)) {
        throw new FileOperationException('Could not chmod dir: ' . $dir, 'chmod', 'dir', $dir);
    }

    return true;
}

/**
 * @param string $file path to file
 * @param $permission
 * @param string $method
 * @param string $url
 * @param bool|string $context
 * @return bool
 * @throws FilesystemCredentialException with S/FTP form if it can't get the required filesystem credentials
 * @throws FileOperationException
 */
function w3_chmod_file($file, $permission, $method = '', $url = '', $context = false) {
    if (!file_exists($file))
        return false;
    if (@chmod($file, $permission))
        return true;

    w3_wp_request_filesystem_credentials($method, $url, $context);

    global $wp_filesystem;

    if (!$wp_filesystem->chmod($file, $permission)) {
        throw new FileOperationException('Could not chmod file: ' . $file, 'chmod', 'file', $file);
    }

    return true;
}

/**
 * Creates maintenance mode file
 * @param $time
 */
function w3_enable_maintenance_mode($time = null) {
    if (is_null($time))
        $time = 'time()';
    w3_wp_write_to_file(w3_get_site_root() . '/.maintenance', "<?php \$upgrading = $time; ?>");
}

/**
 * Deletes maintenance mode file
 */
function w3_disable_maintenance_mode() {
    w3_wp_delete_file(w3_get_site_root() . '/.maintenance');
}

/**
 * Thrown when the plugin fails to get correct filesystem rights when it tries to modify manipulate filesystem.
 */
class FilesystemCredentialException extends Exception
{
    private $method_used;
    private $ftp_form;

    public function __construct($message, $method_used = 'direct', $ftp_form = '') {
        parent::__construct($message);
        $this->method_used = $method_used;
        $this->ftp_form = $ftp_form;
    }

    public function method_used() {
        return $this->method_used;
    }

    public function ftp_form() {
        return $this->ftp_form;

    }
}

/**
 * Thrown when the plugin fails to read, create, delete files and folders.
 */
class FileOperationException extends Exception {
    private $operation;
    private $file_type;
    private $filename;
    public function __construct($message, $operation = '', $file_type = '', $filename = '') {
        parent::__construct($message);
        $this->operation = $operation;
        $this->file_type = $file_type;
        $this->filename = $filename;
    }

    public function getFileType()
    {
        return $this->file_type;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function getOperation()
    {
        return $this->operation;
    }
}