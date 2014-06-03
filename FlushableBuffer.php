/**
 * Base Class-
 * Class FlushableBuffer
 * 
 */
class FlushableBuffer extends \ArrayObject implements Flushable
{
    /**
     * Internal reference to the transport we are currenly using..
     * @var FlushableTransport
     */
    private $transport;
    /** @var int when to flush */
    private $maxBeforeFlush;

    /**
     * $flushAt is what point the buffer is 'full' and we should send it via the transport
     * @param int $flushAt
     */
    public function __construct($flushAt=50){
        $this->maxBeforeFlush=$flushAt;
        parent::__construct();
    }

    /**
     * Special case if we want to force a flush after add
     * @param $key
     * @param $val
     */
    public function addFlush($key,$val){
        $this->offsetSet($key,$val);
        $this->flush();
    }

    /**
     * add Item to the buffer using a specific key, key will be transported with data
     * @param $key
     * @param $val
     */
    public function add($key,$val){

        $this->offsetSet($key,$val);
        if($this->count()>=$this->maxBeforeFlush){
            $this->flush();
        }
    }

    /**
     * Just tack onto the buffer without a specific key..
     * @param mixed $val
     */
    public function append($val){

        parent::append($val);
        if($this->count()>=$this->maxBeforeFlush){
            $this->flush();
        }
    }

    /**
     * force a send via transport and empty the buffer
     * @return string
     */
    public function flush(){
        $re="";
        if($this->count()>0){
            $re=$this->transport->send($this->getArrayCopy());
            foreach($this as $index =>$value){

                $this->offsetUnset($index);
            }
        }
        return $re;
    }

    /**
     * exactly what it says on the tin
     * @param $FlushTransport
     * @throws \Exception
     */
    public function attachTransport($FlushTransport){
        if(!($FlushTransport instanceof FlushTransport)){
            throw new \Exception("Trying to use transport that doesn't implement FlushTransport");
        }else{
            $this->transport=$FlushTransport;
        }

    }


}
