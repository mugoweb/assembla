<?php
namespace MugoWeb;

use Exception;

class Assembla
{

    public $apiKey;
    public $apiSecret;
    public $apiEndpoint;

    /**
     * Constructs an Assembla object
     * @param String $apiKey Assembla API key (see https://api-docs.assembla.cc/content/get_started.html)
     * @param String $apiSecret Assembla API key (see https://api-docs.assembla.cc/content/get_started.html)
     * @param String $apiEndpoint you shouldn't have to change this
     * @throws Exception if $apiKey or $apiSecret are not provided, or an invalid $apiEndpoint is specified
     * @returns Assembla
     */
    public function __construct( $apiKey, $apiSecret, $apiEndpoint = "https://api.assembla.com/v1/" )
    {
        if ( !$apiKey ) {
            throw new Exception( 'You must provide an API key. You can create one at https://app.assembla.com/user/edit/manage_clients.' );
        } else {
            $this->apiKey = $apiKey;
        }

        if ( !$apiSecret ) {
            throw new Exception( 'You must provide an API secret. You can create one at https://app.assembla.com/user/edit/manage_clients.' );
        } else {
            $this->apiSecret = $apiSecret;
        }

        if ( !filter_var( $apiEndpoint, FILTER_VALIDATE_URL ) ) {
            throw new Exception('You must provide a valid API endpoint.');
        } else {
            $this->apiEndpoint = $apiEndpoint;
        }

        return $this;
    }

    public function getSpaces( )
    {
        try {
            $result = $this->apiRequest( 'spaces.json');
        } catch( Exception $e ) {
            return $e;
        }

        return $result;
    }

    public function getCurrentUser( )
    {
        try {
            $result = $this->apiRequest( 'user.json');
        } catch( Exception $e ) {
            return $e;
        }

        return $result;
    }

    public function getMilestones( $spaceId = "" )
    {
        if ( !$spaceId ) {
            throw new Exception( 'You must provide an Assembla space ID.' );
        }
        try {
            $result = $this->apiRequest( 'spaces/' . $spaceId . '/milestones.json');
        } catch( Exception $e ) {
            return $e;
        }

        return $result;
    }

    public function createMilestone($spaceId, $payload )
    {
        if ( !$spaceId ) {
            throw new Exception( 'You must provide an Assembla space ID.' );
        }

        if ( !$payload ) {
            throw new Exception( 'You must provide a JSON representation of the milestone you wish to create (https://api-docs.assembla.cc/content/ref/milestones_create.html).' );
        }

        try {
            $result = $this->apiRequest( 'spaces/' . $spaceId . '/milestones.json', "POST", $payload);
        } catch( Exception $e ) {
            return $e;
        }

        return $result;
    }

    public function updateMilestone($spaceId, $milestoneId, $payload )
    {
        if ( !$spaceId ) {
            throw new Exception( 'You must provide an Assembla space ID.' );
        }

        if ( !$milestoneId ) {
            throw new Exception( 'You must provide an Assembla milestone ID.' );
        }

        if ( !$payload ) {
            throw new Exception( 'You must provide a JSON representation of the milestone updates (https://api-docs.assembla.cc/content/ref/milestones_update.html).' );
        }

        try {
            $result = $this->apiRequest( 'spaces/' . $spaceId . '/milestones/' . $milestoneId, "PUT", $payload);
        } catch( Exception $e ) {
            return $e;
        }

        return $result;
    }

    public function deleteMilestone($spaceId, $milestoneId )
    {
        if ( !$spaceId ) {
            throw new Exception( 'You must provide an Assembla space ID.' );
        }

        if ( !$milestoneId ) {
            throw new Exception( 'You must provide an Assembla milestone ID.' );
        }


        try {
            $result = $this->apiRequest( 'spaces/' . $spaceId . '/milestones/' . $milestoneId, "DELETE", $payload);
        } catch( Exception $e ) {
            return $e;
        }

        return $result;
    }

    public function apiRequest( $apiPath, $requestType = false, $payload = false )
    {
        $requestHeaders = array(
            'Content-Type: application/json',
            'X-Api-Key:' . $this->apiKey,
            'X-Api-Secret:' . $this->apiSecret,
        );

        $ch = $this->getCurlObject(
            $this->apiEndpoint . $apiPath,
            $requestHeaders,
            isset( $requestType ) ? $requestType : false,
            isset( $payload ) ? $payload : false
        );

        $result = curl_exec( $ch );

        if ( curl_error( $ch ) ) {
            return curl_error( $ch );
        } else {
            return $result;
        }
    }

    public function getCurlObject( $url, $headers, $requestType = false, $payload = false, $headerFunction = false )
    {
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );

        if ( $headers ) {
            curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
        }

        if ( $headerFunction ) {
            curl_setopt($ch, CURLOPT_HEADERFUNCTION, $headerFunction );
        }

        curl_setopt( $ch, CURLOPT_FAILONERROR, true );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        if ( $requestType ) {
            switch ($requestType) {
                case "POST":
                    curl_setopt( $ch, CURLOPT_POST, 1 );
                    break;
                case "PUT":
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                    break;
                case "DELETE":
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                    break;
                default:
                    curl_setopt( $ch, CURLOPT_HTTPGET, 1 );
            }
        } else {
            curl_setopt( $ch, CURLOPT_HTTPGET, 1 );
        }

        if ( $payload ) {
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        }

        return $ch;
    }
}
