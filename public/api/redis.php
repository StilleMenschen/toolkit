<?php
header("Content-Type: application/json; charset=UTF-8");
function param_exists($v)
{
    return isset($_REQUEST[$v]) && !empty($_REQUEST[$v]);
}

class Model
{
    public $code;
    public $values;
    public $msg;

    public function __construct()
    {
        $this->code = -1;
        $this->values = array();
        $this->msg = "";
    }
}

class ModelRepository
{
    private $note;
    private $redis;
    private $key;
    private $decode_value;

    public function __construct($key)
    {
        $this->key = 'user:' . $key;
        $this->note = new Model();
        $this->redis = new Redis();
        $this->redis->connect('localhost', 6379);
        $this->decode_value = function ($v) {
            return rawurldecode($v);
        };
    }

    function __destruct()
    {
        $this->redis->close();
    }

    public function add($value)
    {
        if (!empty($value)) {
            $value = rawurlencode($value);
            $this->redis->lPush($this->key, $value);
        }
        return $this->get();
    }

    public function set($pos, $value)
    {
        if (!empty($pos) && !empty($value) && $this->redis->exists($this->key)) {
            $value = rawurlencode($value);
            $this->redis->lSet($this->key, intval($pos, 16), $value);
        }
        return $this->get();
    }

    public function remove_once($value)
    {
        if ($this->redis->exists($this->key)) {
            $value = rawurlencode($value);
            $this->redis->lRem($this->key, $value, 1);
        }
        return $this->get();
    }

    public function remove_all()
    {
        if ($this->redis->exists($this->key)) {
            $this->redis->del($this->key);
        }
        return $this->get();
    }

    public function remove_first()
    {
        if ($this->redis->exists($this->key)) {
            $this->redis->lPop($this->key);
        }
        return $this->get();
    }

    public function remove_last()
    {
        if ($this->redis->exists($this->key)) {
            $this->redis->rPop($this->key);
        }
        return $this->get();
    }

    public function get()
    {
        if ($this->redis->exists($this->key)) {
            $tmp = $this->redis->lrange($this->key, 0, -1);
            $this->note->code = 0;
            $this->note->values = array_map($this->decode_value, $tmp);
            $this->note->msg = 'list size is ' . $this->redis->lLen($this->key);
        } else {
            $this->note->msg = 'Nothing';
        }
        return json_encode($this->note, JSON_UNESCAPED_UNICODE);
    }
}

if (isset($_REQUEST['o']) && isset($_REQUEST['key'])) {
    $o = $_REQUEST['o'];
    $key = $_REQUEST['key'];
    $value = "";
    $pos = "";
    if (param_exists('value')) {
        $value = $_REQUEST['value'];
    }
    if (param_exists('pos')) {
        $pos = $_REQUEST['pos'];
    }
    $repository = new ModelRepository($key);
    switch ($o) {
        case 'all':
            echo $repository->get();
            break;
        case 'add':
            echo $repository->add($value);
            break;
        case 'set':
            echo $repository->set($pos, $value);
            break;
        case 'rm':
            echo $repository->remove_once($value);
            break;
        case 'rmf':
            echo $repository->remove_first();
            break;
        case 'rml':
            echo $repository->remove_last();
            break;
        case 'rma':
            echo $repository->remove_all();
            break;
        default:
            $m = new Model();
            $m->msg = "没有指明操作";
            $m->values = array($o, $key);
            echo json_encode($m, JSON_UNESCAPED_UNICODE);
    }
} else {
    $m = new Model();
    $m->msg = "没有接收到请求";
    echo json_encode($m, JSON_UNESCAPED_UNICODE);
}

