<?php

/**
 * @author vasily SHibanov
 */

namespace ProjectDriver;

require_once 'UploadInterface.php';

require "./library/Aws/aws-autoloader.php";

use Aws\CloudFront\Exception\Exception;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Guzzle\Http\EntityBody;


class UploadAWSDriver implements UploadInterface
{
    private static $_instance = false;

    private $_connection = false;

    private $_options = false;

    public static function getInstance()
    {
        if(!self::$_instance)
            self::$_instance = new self();

        return self::$_instance;
    }

    public function setConnection($connection)
    {
        $this->_connection = $connection;

        return $this->_connection;
    }

    public function getConnection()
    {
        return $this->_connection;
    }

    public function setOptions($options)
    {
        $this->_options = $options;
        return $this->_options;
    }

    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Connect to AWS storage
     * @return $this
     */
    public function connect()
    {
        $options = $this->getOptions();

        // Instantiate an S3 client
        $s3 = S3Client::factory(array(
            'key' => $options['key'],
            'secret' => $options['secret'],
            'signature' => 'v4',
            'region' => $options['region']
        ));

        $this->setConnection($s3);

        return $this;
    }

    /**
     * Put the file in base64 format, with custom identity like md5-hash
     * @param $stream base64Encoded
     * @param $identityKey String hash
     * @return mixed
     */
    public function putFileToStorage($stream, $identityKey)
    {
        if(!$connection = $this->getConnection())
            $connection = $this->connect()->getConnection();

        try {
            $result = $connection->putObject(array(
                'Bucket' => 'pixorum-orig',
                'Key'    => $this->_getPath($identityKey),
                'Body'   => base64_decode($stream),
                'ACL'    => 'public-read',
                'ContentType' => 'image/jpeg'
            ));

            $resAr = $result->toArray();

            return $resAr['ObjectURL'];

        } catch (S3Exception $e) {
           throw new Exception($e);
        }
    }

    private function _getPath($hash, $suffix = "_original", $ext = 'jpg')
    {
        $part1 = substr($hash, 0, 1);
        $part2 = substr($hash, 1, 2);
        $part3 = substr($hash, 3, 3);

        return $part1 . '/' . $part2 . '/' . $part3 . '/' . $hash . $suffix . '.' . $ext;
    }
}

