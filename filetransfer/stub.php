<?php

/**
 * @package   mod_digisem
 * @copyright 2011-2013 Patrick Meyer, Tobias Niedl
 * @license   GNU General Public License (GPL) 3.0 (http://www.gnu.org/licenses/gpl.html)
 */

/**
 * Overwrite this class to use a specific file transfer protocol
 *
 * @author Patrick Meyer
 * @since d#9
 */
abstract class FileTransferStub {
    
    protected $host;
    protected $user;
    protected $pwd;
    
    /**
     * Creates a file transfer instance
     */
    function __construct($host, $user, $pwd) {
        $this->host = $host;
        $this->user = $user;
        $this->pwd = $pwd;
    }
    
    /**
     * Connects to the server via using $host.
     *
     * @return TRUE, if connection to host can be established.
     */
    abstract function connect();
    
    /**
     * Creates a session with the host. 
     * 
     * @return session
     */
    abstract function login();
    
    /**
     * Lists the file names of a directory. 
     * 
     * @param $path Path to the directory
     * @return List of file names
     */
    abstract function listDir($path);
    
    /**
     * Receives the file content from the remote location and saves it locally. 
     *
     * @param $from Remote file path
     * @param $fileHandle Local file path handle
     * @return TRUE, if download and file writing succeeded
     */
    abstract function recvFile($from, $fileHandle);
    
    /**
     * Removes a remote file.
     * 
     * @param $path File path
     * @return TRUE, if file could be deleted
     */
    abstract function remFile($path);
    
    /**
     * Close the session
     */
    abstract function close();
} 
?>
