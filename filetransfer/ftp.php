<?php

/**
 * @package   mod_digisem
 * @copyright 2011-2013 Patrick Meyer, Tobias Niedl
 * @license   GNU General Public License (GPL) 3.0 (http://www.gnu.org/licenses/gpl.html)
 */


/**
 * FTP file transfer handler
 *
 * @author Patrick Meyer
 * @since d#9
 */
class FTPHandler extends FileTransferStub {
    
    /**
     * Connection handle
     */
    protected $conn_id;
    protected $connected = false;
    
    /**
     * Connects to the server via using $host.
     *
     * @return TRUE, if connection to host can be established.
     */
    function connect() {
        $this->conn_id = ftp_connect($this->host);
        
        if ($this->conn_id) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Creates a session with the host. 
     * 
     * @return session
     */
    function login() {
        $this->connected = @ftp_login($this->conn_id, $this->user, $this->pwd);
        
        if ($this->connected) {
            // Fix for bug#19: Cannot list directory content when using a secured conn
	        ftp_pasv($this->conn_id, true);
        }
        
        return $this->connected;
    }
    
    /**
     * Lists the file names of a directory. 
     * 
     * @param $path Path to the directory
     * @return List of file names
     */
    function listDir($path) {
        return ftp_nlist($this->conn_id, $path);
    }
    
    /**
     * Receives the file content from the remote location and saves it locally. 
     *
     * @param $from Remote file path
     * @param $fileHandle Local file path handle
     * @return TRUE, if download and file writing succeeded
     */
    function recvFile($from, $fileHandle) {
        $ret = ftp_nb_fget($this->conn_id, $fileHandle, $from, FTP_BINARY);
		while ($ret == FTP_MOREDATA) {
		    $ret = ftp_nb_continue($this->conn_id);
		} // while
		
		if ($ret == FTP_FINISHED) {
		    return true;
		} else {
		    return false;
		}
    }
    
    /**
     * Removes a remote file.
     * 
     * @param $path File path
     * @return TRUE, if file could be deleted
     */
    function remFile($path) {
        return ftp_delete($this->conn_id, $path);
    }
    
    /**
     * Close the session
     */
    function close() {
        if ($this->connected) {
            ftp_close($this->conn_id);  
        }
    }
} 
?>
