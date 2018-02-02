<?php
/*
 *  Socket
 *  @Autor Damon
 */
class Socket
{
    private $sIP;
    private $iPort;
    private $Socket;

    public function __construct($sIP, $iPort)
    {
        $this->sIP   = $sIP;
        $this->iPort = $iPort;
        try{
            $this->create();
            $this->connect();
            return $this->Socket;
        }catch(\Exception $E){
            return false;
        }
    }

    public function create()
    {
        $this->Socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        //发送超时2秒
        socket_set_option($this->Socket,SOL_SOCKET,SO_RCVTIMEO,array('sec'=>2, 'usec'=>0 ));
        //接收超时4秒
        socket_set_option($this->Socket,SOL_SOCKET,SO_SNDTIMEO,array('sec'=>4, 'usec'=>0 ));
        if ($this->Socket === false) {
            //throw new \Exception('Create Error'.socket_strerror(socket_last_error()));
            throw new \Exception(10001);
        } else {
            return true;
        }
    }

    public function connect()
    {
        $result = socket_connect($this->Socket, $this->sIP, $this->iPort);
        if($result === false) {
            throw new \Exception(
                "Reason: ($result) " . socket_strerror(socket_last_error($this->Socket))
            );
        } else {
            return true;
        }
    }

    public function write($sData)
    {
        return socket_write($this->Socket, $sData, strlen($sData));
    }

    static $bWhileRead = false;

    public function get()
    {
        $sOut = '';
        if (self::$bWhileRead) {
            while ($out = socket_read($this->Socket, 8192)) { $sOut .= $out; }
        }else{
            $sOut = socket_read($this->Socket, 8192);
        }
        return $sOut;
    }

    public function close()
    {
        return socket_close($this->Socket);
    }

    public function send($sData='', &$mRet='')
    {
        try{
            $this->write($sData);
            return $this->get();
        }catch(\Exception $E){
            $mRet = $E->getMessage();
            return false;
        }
    }
}
/*
$Socket = new Socket('localhost', 8585);
$sReturn = $Socket->send('{"sign":"f6821c3211ed8e11a94b749c0ce6450f","sendTo":"telnet","host":"php"}');
if (strlen($sReturn) == 0) {
    die('no return');
}
$aRet = json_decode($sReturn, true);
if($aRet AND isset($aRet['status']) AND $aRet['status'] == 0){
    echo "成功";
}else{
    echo "失败:".$sReturn;
}
*/
