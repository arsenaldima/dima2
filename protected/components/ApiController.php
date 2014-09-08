<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class ApiController extends Controller
{
    /**
     * Константы статусов
     */
    const STATUS_OK = 200;
    const STATUS_NOT_MODIFIED = 304;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_UNAUTHORIZED = 401;
    const STATUS_FORBIDDEN = 403;
    const STATUS_NOT_FOUND = 404;
    const STATUS_INTERNAL_SERVER_ERROR = 500;

    /**
     * @var array Хранит результат выполнения действия
     */
    public $actionResponse = array();

    /**
     * Получить описание кода ответа
     *
     * @param $code
     * @return bool
     */
    public function getStatusMessage($code)
    {
        $messages = array(
            200 => 'OK',
            304 => 'Not Modified',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            420 => 'API Limitations',
            500 => 'Internal Server Error',
        );

        return !empty($messages[$code]) ? $messages[$code] : false;
    }

    /**
     * Отправка ответа клиенту
     *
     * @param $code
     * @param array $data
     */
    public function sendResponse($code, $data = array())
    {
        $response = array();

        if (!is_int($code))
        {
            $data = $code;
            $code = 200;
        }

        // data is empty
        if (!is_array($data) && empty($data))
        {
            $code = 404;
        }

        // if data is model and has errors
        if (is_a($data, 'CActiveRecord'))
        {
            if ($data->hasErrors())
            {
                $code = 400;
                $data = $data->getErrors();
            }
        }

        $statusMessage = $this->getStatusMessage($code);

        if ($code >= 200 && $code < 400)
        {

            if (!$isArray = is_array($data)) $data = array($data);

            foreach ($data as &$model)
            {
                if (is_object($model) && method_exists($model, 'toJSON'))
                    $model = $model->toJSON();
            }

            $response['data'] = $isArray ? $data : reset($data);
        }
        else
        {
            if (!empty($data))
            {
                $response['errors'] = array();
                $response['errorMessages'] = array();
                if (is_array($data))
                {
                    $response['errors'] = $data;
                    foreach($data as $field => $errors)
                    {
                        $response['errorMessages'] = array_merge($response['errorMessages'], is_array($errors) ? $errors : array());
                    }
                }
                else
                {
                    $response['errorMessages'][] = $data;
                    $response['errors']['app'][] = $data;
                }
            }
        }

        header("HTTP/1.0 {$code} {$statusMessage}");
        header('Content-Type: application/json');

        echo CJSON::encode($response);
        Yii::app()->end();
    }


    /**
     * Генерация кода ответа (200/404) из результата выполнения действия
     *
     * @param CAction $action
     * @see $actionResponse
     */
    protected function afterAction($action)
    {
        if (!empty($this->actionResponse))
        {
            $this->sendResponse(200, $this->actionResponse);
        }
        else
        {
            $this->sendResponse(404, $this->actionResponse);
        }
    }


    /**
     *	Генерирует ошибку 400 если действие не найдено
     */
    public function actionMissing()
    {
        $this->sendResponse(400);
    }

    /**
     * В случае успешного создания действия, возвращает CAction, иначе генерирует ошибку 400
     *
     * @param string $actionID
     * @return CAction|CInlineAction|mixed|null
     */
    public function createAction($actionID)
    {
        if ($action = parent::createAction($actionID))
        {
            return $action;
        }
        else
        {
            $this->actionMissing();
        }
    }
}