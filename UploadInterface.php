<?php

namespace ProjectDriver;

interface UploadInterface
{
    /**
     * Primitive singleton
     * @return mixed
     */
    public static function getInstance();

    /**
     * Connect to storage
     * @return mixed
     */
    public function connect();

    /**
     * Set connection
     * @return mixed
     */
    public function setConnection($connection);

    /**
     * Get connection
     * @return mixed
     */
    public function getConnection();

    /**
     * Get the file stream from param. File stream in base64. Need to be converted in binary. Put the file by connection rules.
     * @param $fileStream
     * @return mixed
     */
    public function putFileToStorage($fileStream, $identityKey);

}