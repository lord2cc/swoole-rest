<?php
namespace Swoole\Network\Protocol;
use Swoole;

require_once LIBPATH.'/function/cli.php';
class AppServer extends HttpServer
{
    protected $router_function;
    protected $apps_path;

    function onStart($serv)
    {
        parent::onStart($serv);
        if (empty($this->apps_path))
        {
            if (!empty($this->config['apps']['apps_path']))
            {
                $this->apps_path = $this->config['apps']['apps_path'];
            }
            else
            {
                throw new \Exception("AppServer require apps_path");
            }
        }
        $php = Swoole::getInstance();
        $php->addHook(Swoole::HOOK_CLEAN, function(){
            $php = Swoole::getInstance();
            //模板初始化
            if(!empty($php->tpl))
            {
                $php->tpl->clear_all_assign();
            }
            //还原session
            $php->session->open = false;
            $php->session->readonly = false;
        });
    }

    function onRequest(Swoole\Request $request)
    {
        $this->currentResponse = new Swoole\Response();
        return Swoole::getInstance()->handlerServer($request);
    }
}