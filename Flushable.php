/**
 * Interface that all flushable buffers must impliment
 * Interface Flushable 
 * 
 */
interface Flushable
{
    public function add($key,$val);
    
    public function flush();
    public function attachTransport($FlushTransport);


}
