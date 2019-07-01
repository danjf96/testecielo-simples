<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// use jlcd\Cielo\Resources\CieloPayment;
// use jlcd\Cielo\Resources\CieloCreditCard;
// use jlcd\Cielo\Resources\CieloCustomer;
// use jlcd\Cielo\Resources\CieloOrder;

use Cielo\API30\Merchant;

use Cielo\API30\Ecommerce\Environment;
use Cielo\API30\Ecommerce\Sale;
use Cielo\API30\Ecommerce\CieloEcommerce;
use Cielo\API30\Ecommerce\Payment;
use Cielo\API30\Ecommerce\CreditCard;

use Cielo\API30\Ecommerce\Request\CieloRequestException;

class Pay extends Controller
{
    private $enviroment;
    private $merchant;
    private $cielo;
    private $sale;
    private $payment;
    private $creditCard;

    public function __construct(Request $request){
        //$curl = curl_init('https://apisandbox.cieloecommerce.cielo.com.br/1/sales');

        $this->enviroment = Environment::sandbox();
        $this->merchant = new Merchant(config('cielo.MerchantId'),config('cielo.MerchantKey'));
        $this->cielo = new CieloEcommerce($this->merchant, $this->enviroment);
        $this->payment = Payment::PAYMENTTYPE_CREDITCARD;

    }

    public function index(Request $request){

        $sale = new Sale(uniqid());

        $customer = $sale->customer($request->nome);

        $payment = $sale->payment($request->valor);

        $payment->setType($this->payment)
                ->creditCard($request->cvv, $request->band)
                ->setExpirationDate($request->validade)
                ->setCardNumber($request->card)
                ->setHolder($request->nome);

        try {
            
            $sale = ($this->cielo)->createSale($sale);

            $paymentId = $sale->getPayment()->getPaymentId();

            $sale = ($this->cielo)->captureSale($paymentId, $request->valor, 0);

            return $sale;

           // $sale = (new CieloEcommerce($merchant, $environment))->cancelSale($paymentId, 15700);
        } catch (CieloRequestException $e) {

            $error = $e->getCieloError();
            return array('msg'=>$error->getMessage(),'code'=>$error->getCode());

        }
    }

    public function boleto(Request $request){
        $sale = new Sale(uniqid());

        $customer = $sale->customer($request->nome)
                        ->setIdentity($request->cpf_cnpj)
                        ->setIdentityType($request->typeident)
                        ->address()->setZipCode($request->cep)
                                    ->setCountry('BRA')
                                    ->setState($request->estado)
                                    ->setCity($request->estado)
                                    ->setDistrict('Centro')
                                    ->setStreet($request->rua)
                                    ->setNumber($request->numero);


        $payment = $sale->payment($request->valor)
                        ->setType(Payment::PAYMENTTYPE_BOLETO)
                        ->setAddress('Rua de Teste')
                        ->setBoletoNumber('1234')
                        ->setAssignor('Empresa de Teste')
                        ->setDemonstrative('Desmonstrative Teste')
                        ->setExpirationDate(date('d/m/Y', strtotime('+1 month')))
                        ->setIdentification('11884926754')
                        ->setInstructions('Esse é um boleto de exemplo');


        try {

            $sale = ($this->cielo)->createSale($sale);

            $paymentId = $sale->getPayment()->getPaymentId();
            $boletoURL = $sale->getPayment()->getUrl();

            return array('msg'=>"URL Boleto: ".$boletoURL);
        } catch (CieloRequestException $e) {

            $error = $e->getCieloError();
            return array('msg'=>$error->getMessage(),'code'=>$error->getCode());

        }
    }

}
