<?php

use Payum\Core\Request\GetHumanStatus;
use Payum\LaravelPackage\Controller\PayumController;
use Payum\LaravelPackage\Model\Payment;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends PayumController
{
	public function examples()
	{
//        \Schema::create('payum_payments', function($table) {
//            /** @var \Illuminate\Database\Schema\Blueprint $table */
//            $table->bigIncrements('id');
//            $table->text('details');
//            $table->string('number');
//            $table->string('description');
//            $table->string('clientId');
//            $table->string('clientEmail');
//            $table->string('totalAmount');
//            $table->string('currencyCode');
//            $table->timestamps();
//        });
//        \Schema::create('payum_tokens', function($table) {
//            /** @var \Illuminate\Database\Schema\Blueprint $table */
//            $table->string('hash', 36)->primary();
//            $table->text('details');
//            $table->string('targetUrl');
//            $table->string('afterUrl');
//            $table->string('gatewayName');
//            $table->timestamps();
//        });
//        \Schema::create('payum_gateway_configs', function($table) {
//            /** @var \Illuminate\Database\Schema\Blueprint $table */
//            $table->bigIncrements('id');
//            $table->text('config');
//            $table->string('factoryName');
//            $table->string('gatewayName');
//            $table->timestamps();
//        });

		return \View::make('payment_examples');
	}

    public function done($payum_token)
    {
        /** @var Request $request */
        $request = \App::make('request');
        $request->attributes->set('payum_token', $payum_token);

        $token = $this->getPayum()->getHttpRequestVerifier()->verify($request);

        $gateway = $this->getPayum()->getGateway($token->getGatewayName());

        $gateway->execute($status = new GetHumanStatus($token));

        return \Response::json(array(
            'status' => $status->getValue(),
            'details' => iterator_to_array($status->getFirstModel())
        ));
    }

    public function doneOrder($payum_token)
    {
        /** @var Request $request */
        $request = \App::make('request');
        $request->attributes->set('payum_token', $payum_token);

        $token = $this->getPayum()->getHttpRequestVerifier()->verify($request);

        $gateway = $this->getPayum()->getGateway($token->getGatewayName());

        $gateway->execute($status = new GetHumanStatus($token));

        /** @var Payment $payment */
        $payment = $status->getFirstModel();

        return \Response::json(array(
            'payment' => array(
                'status' => $status->getValue(),
                'client' => array(
                    'id' => $payment->getClientId(),
                    'email' => $payment->getClientEmail(),
                ),
                'number' => $payment->getNumber(),
                'description' => $payment->getCurrencyCode(),
                'total_amount' => $payment->getTotalAmount(),
                'currency_code' => $payment->getCurrencyCode(),
                'details' => $payment->getDetails(),
            ),
        ));
    }
}
