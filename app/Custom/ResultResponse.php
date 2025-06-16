<?php
namespace App\Custom;



class ResultResponse 
{

    const SUCCESS_CODE=200;
    const ERROR_CODE=300;
    const ERROR_ELEMENT_NOT_FOUNT_CODE=404;
    const ERROR_EMAIL_EXISTING_CODE=1062;
    const ERROR_DNI_EXISTING_CODE=1063;
    

    const TXT_SUCCESS_CODE='Success';
    const TXT_ERROR_CODE='Error';
    const TXT_ERROR_ELEMENT_NOT_FOUNT_CODE='Element not fount';
    const TXT_ERROR_EMAIL_EXISTING_CODE='Email Existing';
    const TXT_ERROR_DNI_EXISTING_CODE='Dni Existing';

    public $statusCode;
    public $message;
    public $data;

    function _construct(){
        $this->statusCode=self::ERROR_CODE;
        $this->message='Error';
        $this->data='';
            }

            public function getStatusCode()
            {
                return $this->statusCode;
            }

            public function setStatusCode($statusCode): void
            {
               $this->statusCode=$statusCode;
            }

               public function getMessage()
            {
                return $this->message;
            }
   
            public function setMessage($message): void
            {
               $this->message=$message;
            }


            public function getData()
            {
                return $this->data;
            }
   
            public function setData($data): void
            {
               $this->data=$data;
            }
   


}
