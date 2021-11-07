<?php

// require_once("core/Router.php");
namespace app\core;
use app\controllers\Controller;
use app\core\db\Database;
use app\core\db\DbModel;
use app\models\User;

class Application
{
    public  static string $ROOT_DIR;
    public string $userClass;
    public string $layout = 'main';
    public Router $router;
    public Request $request;
    public  Response  $response;
    public Database $db;
    public static Application $app;
    public ?Controller $controller = null;
    public Session $session;
    public ?UserModel $user;
    public View $view;

    public function __construct($rootPath, array $config)
    {
        $user = new User();
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request,$this->response);
        $this->view = new View();

        $this->db = new Database($config['db']);

//        $user = new User();
        $primaryValue = $this->session->get("user");
        if($primaryValue) {
            $primaryKey = $user->primaryKey();
            $this->user = $user->findOne([$primaryKey => $primaryValue]);
        } else {
            $this->user = null;
        }
    }

    public static function isGuest()
    {
        return !self::$app->user;
    }

    public function run()
    {
        try {
            echo $this->router->resolve();
        }
        catch (\Exception $e)
        {
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView("_error",[
                "exception" => $e
            ]);
        }
    }

    /**
     * @return Controller
     */
    public function getController(): Controller
    {
        return $this->controller;
    }

    /**
     * @param Controller $controller
     */
    public function setController(Controller $controller): void
    {
        $this->controller = $controller;
    }

    public function login(UserModel $user)
    {
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set("user",$primaryValue);
        return true;
    }

    public function logout()
    {
        $this->user = null;
        $this->session->remove("user");
    }
        
}

?>