<?php

/**
 * pCloud service API wrapper using HTTP interface
 *
 * @link https://docs.pcloud.com/
 * @author Ilian Iliev
 * @since 12.06.16
 */

class pCloud
{

    const MAIN_URL = 'http://api.pcloud.com/';

    private $token = null;
    private $userData = null;
    private $methods = [
        'userinfo',
        'uploadfile',
        'getfilepublink'
    ];

    /**
     * Login user if username and password are passed
     *
     * @param String $username Optional. Username
     * @param String $password Optional. Passowrd
     */
    function __construct($username = null, $password = null) {
        if (is_string($username) && is_string($password)) {
            $this->login($username, $password);
        }
    }

    /**
     * Gets user token
     *
     * @return String User token
     */
    private function getToken()
    {
        if (is_null($this->token)) {
            throw new Exception('No user token. Call "login" first.');
        }
        return $this->token;
    }

    /**
     * Gets API method URI by method name
     *
     * @param String $methodName API method name
     * @return String Method URI
     */
    private function getMethodUrl($methodName)
    {
        if (! in_array($methodName, $this->methods)) {
            throw new Exception('No such API method');
        }
        return self::MAIN_URL . $methodName;
    }

    /**
     * Send http post request
     *
     * @param String $url Request URI
     * @param Array $data Request send params
     * @return Object Response
     */
    private function httpPost($url, $data)
    {
        $curl = curl_init($url);

        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true
        ]);

        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        return $response;
    }

    /**
     * Send http put request
     *
     * @param String $url Request URI
     * @param String $file_path Target file path
     * @param Array $get_params Request get params
     * @return Object Response
     */
    private function httpPut($url, $file_path, $get_params) {

        if ( isset($get_params)) {
            $url .= '?' . http_build_query($get_params);
        }

        $curl = curl_init();
        $fh_res = fopen($file_path, 'r');

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_PUT => 1,
            CURLOPT_INFILE => $fh_res,
            CURLOPT_INFILESIZE => filesize($file_path),
            CURLOPT_RETURNTRANSFER => 1
        ]);

        $response = json_decode(curl_exec($curl));

        fclose($fh_res);

        return $response;
    }

    /**
     * Login user
     *
     * @param String $username Username
     * @param String $password PAssword
     * @return Object User data
     */
    public function login($username, $password)
    {
        $this->userData = $this->httpPost($this->getMethodUrl('userinfo'), [
            'username' => $username,
            'password' => $password,
            'getauth' => 1,
        ]);

        $this->token = isset($this->userData->auth) ? $this->userData->auth : null;
        return $this->userData;
    }

    /**
     * Upload local file by file path and folder path or folder id
     *
     * @param String $filename Local file name
     * @param String $folderPathOrId pCloud folder path or folder id
     * @param Function $callback Executes with file data passed after upload
     * @return Object Upload data
     */
    public function upload($filename, $folderPathOrId, $callback = null)
    {
        $params = [
            'auth' => $this->getToken(),
            'filename' => $filename
        ];

        if ( is_string($folderPathOrId)) {
            $params['path'] = $folderPathOrId;
        } else if ( is_numeric($folderPathOrId)) {
            $params['folderid'] = $folderPathOrId;
        } else {
            throw new Exception('You must pass folder path or folder id as second argument.');
        }

        $uploadData = $this->httpPut($this->getMethodUrl('uploadfile'), $filename, $params);

        if (! is_null($callback) && is_callable($callback)) {
            $callback($uploadData->metadata[0]);
        }

        return $uploadData;
    }

    /**
     * Get uploaded file public link by folder path or folder id
     *
     * @param String $filePathOrId pCloud file path or file id
     * @param Array $opt {
     *
     *     Optional. An array of arguments.
     *
     *     datetime expire Datetime when the link will stop working.
     *     int maxdownloads Maximum number of downloads for this file
     *     int maxtraffic Maximum traffic that this link will consume (in bytes, started downloads will not be cut to fit in this limit)
     *     int shortlink If set, a short link will also be generated
     * }
     * @return String Public link
     */
    public function getfilepublink($filePathOrId, $opt = [])
    {
        $params = [
            'auth' => $this->getToken()
        ];

        if ( is_string($filePathOrId)) {
            $params['path'] = $filePathOrId;
        } else if ( is_numeric($filePathOrId)) {
            $params['fileid'] = $filePathOrId;
        } else {
            throw new Exception('You must pass file path or file id as first argument.');
        }

        $linkData = $this->httpPost($this->getMethodUrl('getfilepublink'), array_merge($opt, $params));

        return $linkData->link;
    }

    /**
     * Gets logged user data
     *
     * @return Object User data
     */
    public function getUserData()
    {
        return $this->userData;
    }
}
