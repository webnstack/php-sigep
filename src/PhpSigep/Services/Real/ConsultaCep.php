<?php
namespace PhpSigep\Services\Real;

use PhpSigep\Model\ConsultaCepResposta;
use PhpSigep\Services\Result;

/**
 * @author: Stavarengo
 */
class ConsultaCep
{

    public function execute($cep)
    {
        $cep = preg_replace('/[^\d]/', '', $cep);

        $soapArgs = array(
            'cep' => $cep,
        );

        $r = SoapClientFactory::getSoapClient()->consultaCep($soapArgs);

        $errorCode = null;
        $errorMsg  = null;
        $result    = new Result();
        if (!$r) {
            $errorCode = 0;
        } else if ($r instanceof \SoapFault) {
            $errorCode = $r->getCode();
            $errorMsg  = SoapClientFactory::convertEncoding($r->getMessage());
            $result->setSoapFault($r);
        } else if ($r instanceof \stdClass) {
             if (property_exists($r, 'return') && $r->return instanceof \stdClass) {
                $consultaCepResposta = new ConsultaCepResposta();
				
				$bairro = isset(SoapClientFactory::convertEncoding($r->return->bairro)) ? SoapClientFactory::convertEncoding($r->return->bairro) : "";
                $consultaCepResposta->setBairro($bairro);
				$cep = isset($r->return->cep) ? $r->return->cep : "";
                $consultaCepResposta->setCep($cep);
				$cidade = isset(SoapClientFactory::convertEncoding($r->return->cidade)) ? SoapClientFactory::convertEncoding($r->return->cidade) : "";
                $consultaCepResposta->setCidade($cidade);
				$complemento = isset(SoapClientFactory::convertEncoding($r->return->complemento)) ? SoapClientFactory::convertEncoding($r->return->complemento) : "";
                $consultaCepResposta->setComplemento1($complemento);
				$complemento2 = isset(SoapClientFactory::convertEncoding($r->return->complemento2)) ? SoapClientFactory::convertEncoding($r->return->complemento2) : "";
                $consultaCepResposta->setComplemento2($complemento2);
				$end = isset(SoapClientFactory::convertEncoding($r->return->end)) ? SoapClientFactory::convertEncoding($r->return->end) : "";
                $consultaCepResposta->setEndereco($end);
				$id = isset($r->return->id) ? $r->return->id : "";
                $consultaCepResposta->setId($id);
				$uf = isset($r->return->uf) ? $r->return->uf : "";
                $consultaCepResposta->setUf($uf);
                $result->setResult($consultaCepResposta);
             } else {
				 $errorCode = 0;
				 $errorMsg = "Resposta em branco. Confirme se o CEP '$cep' realmente existe.";
             }
        } else {
            $errorCode = 0;
            $errorMsg  = "A resposta do Correios não está no formato esperado.";
        }

        $result->setErrorCode($errorCode);
        $result->setErrorMsg($errorMsg);

        return $result;
    }

}
