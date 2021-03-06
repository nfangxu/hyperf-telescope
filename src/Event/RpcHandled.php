<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Wind\Telescope\Event;

class RpcHandled
{
    public $request;

    public $response;

    public $middlewares;

    public function __construct($request, $response, array $middlewares)
    {
        $this->request = $request;
        $this->response = $response;
        $this->middlewares = $middlewares;
    }
}
