<?php

/*
 * This file is part of the Blast Project package.
 *
 * Copyright (C) 2015-2017 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blast\TestsBundle\Api;

use PHPUnit\Framework\TestCase;

class BlastApiTestCase extends TestCase
{
    /********** Test Api **********/
    protected $token;
    protected $result;
    
    /********** HTTPResult **********/
    protected $data;
    protected $status;
    protected $resource;


    /********** Test Api **********/
    public function printResult($endpoint, $action)
    {
        if ($this->show_results) {
            echo $this->getData()."\n";
        }
        echo $this->lastCurlEquivalent."\n\n";
        $this->lastCurlEquivalent = '';
        
        echo "$endpoint | $action | ";
        echo !$this->isSuccess() ? 'ERROR' : 'SUCCESS';
        echo ': HTTP '.$this->getStatus();
        
        echo "\n\n\n";
        return $this;
    }
    
    public function request($uri, $method = 'GET', array $data = [])
    {
        $ch = curl_init($this->base.$uri);
        
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $headers = ['Content-Type: application/json'];
        if ($this->token) {
            $headers[] = 'Authorization: Bearer '.$this->token;
        }
        
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_CUSTOMREQUEST  => $method,
        ]);
        
        $h = implode('" -H "', $headers);

        $this->lastCurlEquivalent = sprintf(
            '$ curl -k "%s" -H "%s" -X %s %s',
            str_replace(
                ['[', ']', ' '],
                ['\\[', '\\]', '%20'],
                $this->base.$uri
            ),
            $h,
            $method,
            $data ? "--data '".json_encode($data)."'" : ''
        );
        
        getHTTPResult($ch);
        
        curl_close($ch);
        return $res;
    }

    
    /********** HTTPResult **********/
    
    public function getHTTPResult($curlResource)
    {
        $this->resource = $curlResource;
        $this->data = curl_exec($curlResource);
        $this->status = curl_getinfo($curlResource, CURLINFO_HTTP_CODE);
    }
    
    public function getCurlResource()
    {
        return $this->resource;
    }
    
    public function getData($json = false)
    {
            return $json ? json_decode($this->data, true) : $this->data;
    }
    
    public function getStatus()
    {
        return $this->status;
    }
    
    public function isSuccess()
    {
        return $this->status < 400;
    }
    
    public function __toString()
    {
        return is_array($this->data) ? json_encode($this->data, JSON_PRETTY_PRINT) : $this->data;
    }

    /**
     * Returns one entity from a list of entities
     *
     * $i   integer if < 0, it means that we are expecting a random result
     * @todo : maybe add ['_embedded']['items'] as param (or not)
     */
    public function getOneFromList($i = -1)
    {
        $data = $this->getData(true)['_embedded']['items'];
        return $i < 0 ? $data[rand(0, count($data)-1)] : $data[$i];
    }
}
