<?php

namespace Yansongda\Pay\Gateways\Wechat;

<<<<<<< HEAD
<<<<<<< HEAD
use Yansongda\Pay\Exceptions\InvalidArgumentException;

class ScanGateway extends Wechat
{
    /**
     * get trade type config.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @return string
     */
    protected function getTradeType()
    {
        return 'NATIVE';
    }

    /**
     * pay a order using modelTWO.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param array $config_biz
     *
     * @return string
     */
    public function pay(array $config_biz = [])
    {
        if (is_null($this->user_config->get('app_id'))) {
            throw new InvalidArgumentException('Missing Config -- [app_id]');
        }

        return $this->preOrder($config_biz)['code_url'];
=======
=======
>>>>>>> dc81d773ef8393de8716681e5c19d1579978ea74
use Symfony\Component\HttpFoundation\Request;
use Yansongda\Pay\Log;
use Yansongda\Supports\Collection;

class ScanGateway extends Gateway
{
    /**
     * Pay an order.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param string $endpoint
     * @param array  $payload
     *
     * @throws \Yansongda\Pay\Exceptions\GatewayException
     * @throws \Yansongda\Pay\Exceptions\InvalidArgumentException
     * @throws \Yansongda\Pay\Exceptions\InvalidSignException
     *
     * @return Collection
     */
    public function pay($endpoint, array $payload): Collection
    {
        $payload['spbill_create_ip'] = Request::createFromGlobals()->server->get('SERVER_ADDR');
        $payload['trade_type'] = $this->getTradeType();

        Log::info('Starting To Pay A Wechat Scan Order', [$endpoint, $payload]);

        return $this->preOrder($payload);
    }

    /**
     * Get trade type config.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @return string
     */
    protected function getTradeType(): string
    {
        return 'NATIVE';
<<<<<<< HEAD
>>>>>>> dev
=======
>>>>>>> dc81d773ef8393de8716681e5c19d1579978ea74
    }
}
