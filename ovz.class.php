<?php
/*
 * This class has been written by EnguerrandP for public release on github.com
 * It is designed for interacting with ovz-webpanel and it's API.
 * Still in development, this is first version, codes will be improved.
 * If you think you can help, don't hesitate to code and post pull request.
 */
class ovzVPS
{
    protected $host;
    protected $account;
    protected $password;

    public function __construct($host, $account, $password)
    {
        if(!empty($host) && !empty($account) && !empty($password)){
            $this->host = $host;
            $this->account = $account;
            $this->password = $password;
        }
        else
            throw new Exception("All fields are required !");
    }

    public function list_nodes()
    {
        $this->_call("/api/hardware_servers/list");
    }

    public function get_ct_informations($id)
    {
        $return = array();
        $call = $this->_call("/api/virtual_servers/get?id=".$id);
        $return['ctID'] = $call['identity'];
        $return['hostname'] = $call['host_name'];
        $return['ip'] = $call['ip_address'];
        $return['start_on_boot'] = $call['start_on_boot'];
        $return['state'] = $call['state'];
        return ($return);
    }

    public function start_ct($id)
    {
        if(empty($id)){
            return (false);
        }
        $call = $this->_call("/api/virtual_servers/start?id=".$id);
        if($call['status']){
            return (true);
        }
        else
            return (false);
    }

    public function stop_ct($id)
    {
        if(empty($id)){
            return (false);
        }
        $call = $this->_call("/api/virtual_servers/stop?id=".$id);
        if($call['status']){
            return (true);
        }
        else
            return (false);
    }

    public function restart_ct($id)
    {
        if(empty($id)){
            return (false);
        }
        $call = $this->_call("/api/virtual_servers/restart?id=".$id);
        if($call['status']){
            return (true);
        }
        else
            return (false);
    }

    public function set_ct_password($id, $password)
    {
        if(empty($id) || empty($password)){
            return (false);
        }
        $call = $this->_call("/api/virtual_servers/update?id=".$id."&password=".$password);
        if($call['status']){
            return (true);
        }
        else
            return (false);
    }

    public function delete_ct($id)
    {
        if(empty($id)){
            return (false);
        }
        $call = $this->_call("/api/virtual_servers/delete?id=".$id);
        if($call['status']){
            return (true);
        }
        else
            return (false);
    }

    private function _call($method)
    {
        $context = stream_context_create(array(
            'http' => array(
                'header'  => "Authorization: Basic " . base64_encode($this->account.":".$this->password)
            )
        ));

        $result = file_get_contents("http://".$this->host.$method, false, $context);

        $doc = simplexml_load_string($result);
        $xml = json_decode(json_encode((array) $doc), 1);
        return ($xml);
    }
}
?>