<?php

namespace App\Service;

use Monolog\DateTimeImmutable;

class JWTService 
{
    
    /**
     * Generate JWT
     * @param array $header
     * @param array $payload
     * @param string $secret
     * @param int $validity
     * @return string
     */
    
    public function generateToken(array $header, array $payload, string $secret, int $validity = 10800):string
    {
        if($validity>0){
            //token's validity: start and end time
            $now = new \DateTimeImmutable();
            $expire = $now->getTimeStamp() + $validity;

            //payload data
            $payload['iat'] = $now->getTimeStamp();
            $payload['exp'] = $expire;
        }

        //encoding header and payload with base64
        $encodedHeader = base64_encode(json_encode($header));
        $encodedPayload = base64_encode(json_encode($payload));

        //replacing +,/ and =
        $encodedHeader = str_replace(['+','/','='],['-','_',''],$encodedHeader);
        $encodedPayload = str_replace(['+','/','='],['-','_',''],$encodedPayload);

        // signature's creation
        $secret = base64_encode($secret);
        $signature = hash_hmac('sha256', $encodedHeader . '.' . $encodedPayload, $secret, true);
        $encodedSignature = base64_encode($signature);
        $encodedSignature = str_replace(['+','/','='],['-','_',''],$encodedSignature);

        //token's creation
        $jwt = $encodedHeader . '.' . $encodedPayload . '.' . $encodedSignature;
       

        return $jwt;
    }

    /**
     * Check format's token
     * @param string $token
     * @return bool
     */
    public function isFormatTokenValid(string $token): bool
    {
        //token contents 3 alphanumeriq groups?
        return preg_match(
            '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
            $token
        ) === 1;
    }

    /**
     * Header's content
     * @param string $token
     * @return array
    */
    public function getHeader(string $token): array
    {
        $array = explode('.',$token);
        $header = json_decode(base64_decode($array[0]),true);
        return $header;
    }

    /**
     * Payload's content
     * @param string $token
     * @return array
    */
    public function getPayload(string $token): array
    {
        $array = explode('.',$token);
        $payload = json_decode(base64_decode($array[1]),true);
        return $payload;
    }

    /**
     * Check if token still valid in terms of time
     * @param string $token
     * @return bool
    */
    public function isValidityTokenValid(string $token): bool
    {
        $payload = $this->getPayload($token);
        $now = new \DateTimeImmutable();
        return $payload['exp'] < $now->getTimeStamp();
    }

    /**
     * Check if token's signature valid by comparison with new token generated with same data
     * @param string $token
     * @param string $secret
     * @return bool
    */
    public function isSignatureTokenValid(string $token, string $secret): bool
    {
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);
        //new token with same data
        $verifyToken = $this->generateToken($header, $payload, $secret, 0);
        return $token === $verifyToken;
    }


}