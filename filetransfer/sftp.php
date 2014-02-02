<?php

/**
 * @package   mod_digisem
 * @copyright 2011-2013 Patrick Meyer, Tobias Niedl
 * @license   GNU General Public License (GPL) 3.0 (http://www.gnu.org/licenses/gpl.html)
 */

/**
 * Overwrite this class to use a specific file transfer protocol
 *
 * @author Tobias Niedl
 * @since d#9
 */
class SFTPHandler extends FileTransferStub {
    
    /**
     * Connection handle
     */
    protected $ssh_resource = null;     //ID of the ssh connection
    protected $ftp_resource = null;     //ID of the ftp connection
    protected $connected = false;       //Set to true after a successful login
    
    /**
     * Connects to the server via using $host.
     *
     * @return TRUE, if connection to host can be established.
     */
    function connect() {

        //Open connection to the remote host via SSH
        $this->ssh_resource = ssh2_connect($this->host, 22);

        //Login on the remote host
        if ($this->ssh_resource) 
        {
            $this->connected = ssh2_auth_password($this->ssh_resource, $this->user, $this->pwd);
            

            if ($this->connected)
            { 
                //Login successful. Request the SFTP subsystem
                $this->ftp_resource = ssh2_sftp($this->ssh_resource);

                //Check if SFTP connection was established
                if ($this->ftp_resource)
                {
                    return true;
                }
            }
        }

        return false;

    }
    
    /**
     * Creates a session with the host. 
     * 
     * @return session
     */
    function login() {
        
        //The login procedure is done at the connect() method as part of opening a FTP connection over SSH
        
        return $this->connected;
    }
    
    /**
     * Lists the file names of a directory. 
     * 
     * @param $path Path to the directory
     * @return List of file names
     */
    function listDir($path) {
        return scandir("ssh2.sftp://" . $this->ftp_resource . $path);
    }
    
    /**
     * Receives the file content from the remote location and saves it locally. 
     *
     * @param $remotePath Remote file path
     * @param $localFileHandle Local file path handle
     * @return TRUE, if download and file writing succeeded
     */
    function recvFile($remotePath, $localFileHandle) {

        //The returned value
        $success = false;


        if ($localFileHandle)
        {
            //Open a stream to the remote file
            $remoteStream = fopen("ssh2.sftp://" . $this->ftp_resource . $remotePath, "rb");
            
            //Read the file content
            $remoteContent = stream_get_contents($remoteStream); //stream_get_contents requires PHP 5 or higher

            if ($remoteContent !== false)
            {
                //Write the file content to the local file, given by the $fileHandler
                if (fwrite($localFileHandle, $remoteContent))
                {
                    $success = true;
                }
            }

            //Close remote stream
            fclose($remoteStream);
        }

        return $success;
    }
    
    /**
     * Removes a remote file.
     * 
     * @param $path File path
     * @return TRUE, if file could be deleted
     */
    function remFile($path) {
        return ssh2_sftp_unlink($this->ftp_resource, $path);
    }
    
    /**
     * Close the session
     */
    function close() {
        ssh2_exec($this->ssh_resource, 'exit');
        unset($this->ssh_resource);
    }
} 
?>
