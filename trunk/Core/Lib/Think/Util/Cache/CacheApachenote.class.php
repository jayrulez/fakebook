<?php 

class CacheApachenote extends Cache
{
    public function __construct($options='')
    {
        if(empty($options)){
            $options = array(           
                'host' => '127.0.0.1',
                'port' => 1042,
                'timeout' => 10
        );
        }
        $this->handler = null;
        $this->open();
        $this->options = $options;
        $this->type = strtoupper(substr(__CLASS__,6));

    }

    public function isConnected()
    {
        return $this->connected;
    }

     public function get($name)
     {
         $this->open();
         $s = 'F' . pack('N', strlen($name)) . $name;
         fwrite($this->handler, $s);

         for ($data = ''; !feof($this->handler);) {
             $data .= fread($this->handler, 4096);
         }
		$this->Q(1);
         $this->close();
         return $data === '' ? '' : unserialize($data);
     }

    public function set($name, $value)
    {
		$this->W(1);
		$this->open();
        $value = serialize($value);
        $s = 'S' . pack('NN', strlen($name), strlen($value)) . $name . $value;

        fwrite($this->handler, $s);
        $ret = fgets($this->handler);
        $this->close();
        $this->setTime[$name] = time();
        return $ret === "OK\n";
    }

     public function rm($name)
     {
         $this->open();
         $s = 'D' . pack('N', strlen($name)) . $name;
         fwrite($this->handler, $s);
         $ret = fgets($this->handler);
         $this->close();

         return $ret === "OK\n";
     }

     private function close()
     {
         fclose($this->handler);
         $this->handler = false;
     }

     private function open()
     {
         if (!is_resource($this->handler)) {
             $this->handler = fsockopen($this->options['host'], $this->options['port'], $_, $_, $this->options['timeout']);
             $this->connected = is_resource($this->handler);         
         }
     }
}

?>