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
				
				$bairro = '';
				if (!empty($r->return->bairro) && null !== $r->return->bairro) {
					$bairro = SoapClientFactory::convertEncoding($r->return->bairro);
				}
				$consultaCepResposta->setBairro($bairro);
                
				$cep = '';
				if (!empty($r->return->cep) && null !== $r->return->cep) {
					$cep = SoapClientFactory::convertEncoding($r->return->cep);
				}
                $consultaCepResposta->setCep($cep);
				
				$cidade = '';
				if (!empty($r->return->cidade) && null !== $r->return->cidade) {
					$cidade = SoapClientFactory::convertEncoding($r->return->cidade);
				}
                $consultaCepResposta->setCidade($cidade);
				
				$complemento = '';
				if (!empty($r->return->complemento) && null !== $r->return->complemento) {
					$complemento = SoapClientFactory::convertEncoding($r->return->complemento);
				}
				$consultaCepResposta->setComplemento1($complemento);
				
				$complemento2 = '';
				if (!empty($r->return->complemento2) && null !== $r->return->complemento2) {
					$complemento2 = SoapClientFactory::convertEncoding($r->return->complemento2);
				}
				$consultaCepResposta->setComplemento2($complemento2);
				
				$end = '';
				if (!empty($r->return->end) && null !== $r->return->end) {
					$end = SoapClientFactory::convertEncoding($r->return->end);
				}
				$consultaCepResposta->setEndereco($end);
				
				$id = '';
				if (!empty($r->return->id) && null !== $r->return->id) {
					$id = $r->return->id;
				}
                $consultaCepResposta->setId($id);
				
                $uf = '';
				if (!empty($r->return->uf) && null !== $r->return->uf) {
					$uf = $r->return->uf;
				}
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
