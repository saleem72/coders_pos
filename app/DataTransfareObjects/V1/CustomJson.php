<?php

namespace App\DataTransfareObjects\V1;




class CustomJson
{
    public bool $status;
    public string|null $message;
    public  $data;


    public function __construct( $status = false,  $message = Null, $data = Null)
    {
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
    }

    public function toArray(): Array {
        return [
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data
        ];
    }
}
