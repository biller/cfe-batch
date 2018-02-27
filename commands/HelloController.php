<?php

namespace app\commands;

use GuzzleHttp\Client;
use Yii;
use yii\console\Controller;

class HelloController extends Controller {

    public $entrada = 'entrada.json';
    public $salida = 'logs/log';
    public $token = '';
    public $dominio = 'https://test.biller.uy/v1/';

    public function actionIndex() {
        $str = file_get_contents(Yii::$app->basePath . '/' . $this->entrada);
        $body = json_decode($str, true);

        $clientes = $body['cliente'];
        unset($body['cliente']);
        $temp = array();
        foreach ($clientes as $value) {
            $copy = $body;

            $copy['cliente'] = $value;

            $copy = json_encode($copy);

            $client = new Client([
                'base_uri' => $this->dominio,
            ]);
            $r = $client->request('POST', 'comprobantes/crear', [
                'body' => $copy,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/json'
                ]
            ]);
            $log = json_decode($r->getBody(), true);
            $temp[] = $log;
        }
        $out = json_encode($temp, JSON_PRETTY_PRINT);
        file_put_contents(Yii::$app->basePath . '/' . $this->salida . ' ' . date("Y-m-d (H_i_s)") . '.json', $out, FILE_APPEND);
    }

}
