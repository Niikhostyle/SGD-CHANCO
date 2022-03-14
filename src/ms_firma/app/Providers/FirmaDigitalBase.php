<?php

// Include REST CURL Library
//require_once('libraries/php-rest-curl/rest.inc.php');

// Include JWT Library
require_once('JWT.php');

// Include S3 Library
//require_once(APPPATH.'libraries/S3.php');

/**
 * Class FirmaDigitalBase
 */
abstract class FirmaDigitalBase
{

    /** @var string  */
    const HASH_FILE_ALGO = 'sha256';

    /** @var string $api API enpoint URL without end '/' */
    protected $api;

    /** @var string $tokenKey API token key */
    protected $tokenKey;

    /** @var string $secretKey API secret key */
    protected $secretKey;

    /** @var string $run user RUN */
    protected $run;

    /** @var string $otp user One-time password */
    protected $otp;

    /** @var string $entity user institution */
    protected $entity;

    /** @var string $purpose */
    protected $purpose;

    /** @var array $files */
    protected $files;

    /** @var S3 $s3 S3 library */
    protected $s3;

    /** @var string $s3bucket S3 bucket name */
    protected $s3bucket;


    /**
     * FirmaDigital constructor.
     *
     * @param array $config configuration params
     *
     * @throws Exception
     */
    public function __construct($config)
    {
        if (is_array($config)) {
            foreach ($config as $param => $value) {
                if (empty($value)) {
                    throw new Exception("No se encontro el parámetro '{$param}' en la configuración");
                }
            }

            $this->api = rtrim($config[ 'api' ], '/');
            $this->tokenKey = trim($config[ 'tokenKey' ]);
            $this->secretKey = trim($config[ 'secretKey' ]);
            $this->purpose = $config[ 'purpose' ];
            $this->entity = $config[ 'entity' ];
            $this->files = array();
            $this->run = '';
        }

        //log_message('debug', 'API Firma Digital Class Initialized');
    }

    /**
     * Set user RUN
     *
     * @param string $run
     *
     * @return $this
     */
    abstract public function setRUN($run);


    /**
     * Set user One-time password (OTP)
     * @param $otp
     *
     * @return $this
     */
    abstract public function setOTP($otp);

    /**
     * Prepare PDF to sign
     *
     * @param string $file   full path and name
     * @param string $description
     * @param array  $layout file config to insert image
     *
     * @return $this
     * @throws Exception
     */
    abstract public function addPDF($file, $description = '', $layout = null);


    /**
     * Remove all files
     *
     * @return $this
     */
    public function removeFiles()
    {
        $this->files = array();

        return $this;
    }


    /**
     * Sign files
     *
     * @return array
     */
    abstract public function sign();


    /**
     * Añade configuración de conexión con Amazon S3
     *
     * @param S3 $s3
     * @param string $bucket
     *
     * @return $this
     */
    public function setS3(S3 $s3, $bucket)
    {
        $this->s3 = $s3;
        $this->s3bucket = $bucket;

        return $this;
    }

    /**
     * Encode file
     *
     * @param string $file name and path
     *
     * @return string
     * @throws Exception
     */
    protected function encodeFile($file)
    {
        try {
            $binary = $this->getFileContent($file);

            return base64_encode($binary);
        } catch (Exception $e) {
            throw $e;
        }
    }


    /**
     * Get file content
     *
     * @param string $filename name and path
     *
     * @return false|string
     * @throws Exception
     */
    protected function getFileContent($filename)
    {
        if ( ! file_exists($filename)) {
            throw new Exception("Archivo no encontrado {$filename}");
        }
        $file_handler = fopen($filename, 'r');
        if ( ! $file_handler) {
            throw new Exception("No se puede acceder al archivo {$filename}");
        }
        $fileContent = fread($file_handler, filesize($filename));
        fclose($file_handler);

        return $fileContent;
    }


    /**
     * get file data
     *
     * @param string $filename
     *
     * @return array
     * @throws Exception
     */
    protected function getObject($filename)
    {
        if ($this->s3) {
            // file in S3
            return $this->getS3object($filename);
        } else {
            // file in local storage
            return array(
                'binary' => $this->getFileContent($filename),
                'hash'   => hash_file(self::HASH_FILE_ALGO, $filename),
                'type'   => 'application/pdf'
            );
        }
    }


    /**
     * get s3 object data
     *
     * @param $filename
     *
     * @return array
     * @throws Exception
     */
    protected function getS3object($filename)
    {
        $obj = $this->s3->getObject($this->s3bucket, $filename, false);
        if (!isset($obj->error) || $obj->error) {
            throw new Exception("Objeto no encontrado {$filename}");
        }

        return array(
            'binary' => $obj->body,
            'hash'   => hash(self::HASH_FILE_ALGO, $obj->body),
            'type'   => $obj->headers[ 'type' ]
        );
    }


    /**
     * Generate insert image layout
     *
     * @param array $config
     *
     * @return string
     * @throws Exception
     */
    protected function generateLayout($config)
    {
        try {
            $encodedFile = $this->encodeFile($config[ 'filename' ]);
        } catch (Exception $e) {
            throw $e;
        }

        return "<AgileSignerConfig><Application id=\"THIS-CONFIG\"><pdfPassword/><Signature>".
            "<Visible active=\"true\" layer2=\"false\" label=\"true\" pos=\"1\">".
            "<llx>{$config['llx']}</llx><lly>{$config['lly']}</lly><urx>{$config['urx']}</urx><ury>{$config['ury']}</ury>".
            "<page>{$config['page']}</page><image>BASE64</image><BASE64VALUE>{$encodedFile}</BASE64VALUE>".
            "</Visible></Signature></Application></AgileSignerConfig>";
    }
}