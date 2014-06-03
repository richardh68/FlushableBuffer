/**
 * This is a sample transport to send JSON data to a remote API Location.
 * Class httpTransport
 * @package Ratchet\Website\Chat
 */
class HttpTransport implements FlushTransport
{
    private $apiEndpoint;
    private $apiKey;
    public function __construct($apiEndpoint,$apiKey){
        $this->apiEndpoint=$apiEndpoint;
        $this->apiKey=$apiKey;
    }
    public function send($Flushable){

        $jsonData=json_encode(array('v'=>$this->generateValidation($Flushable),'data'=>$Flushable) );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => $jsonData,
                'header'=>  "Content-Type: application/json\r\n" .
                    "Content-Length:".strlen($jsonData)." \r\n"
            )
        );

        $context  = stream_context_create( $options );
        $result = file_get_contents( $this->apiEndpoint, false, $context );
      //  echo "\n Begin****".$this->apiEndpoint."****\n".$result."\n ****END ".$this->apiEndpoint."****\n";
        $response = json_decode( $result,true );
        if($response['success']=='true'){return true;}else{return false;}
    }

    function generateValidation($data){


        return $this->hmac(implode("", json_encode($data)),$this->apiKey);
    }
//hmac function
    function hmac($data, $key='', $hash = 'md5', $blocksize = 64)
    {


        if (strlen($key) > $blocksize)
        {
            $key = pack('H*', $hash($key));
        } else
        {
            $key = $key;
        }

        $key = str_pad($key, $blocksize, chr(0));
        $ipad = str_repeat(chr(0x36), $blocksize);
        $opad = str_repeat(chr(0x5c), $blocksize);

        return $hash(($key ^ $opad) . pack('H*', $hash(($key ^ $ipad) . $data)));
    }


}
