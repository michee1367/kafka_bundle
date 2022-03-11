<?php
namespace Mink67\KafkaConnect\Services\Utils;

use DateTimeInterface;
use Mink67\KafkaConnect\Constant;
use Rakit\Validation\Validator;

/**
 * 
 */
class MessageDbValidator {
    
    /**
     * 
     */
    public function __construct() {
    }

    /**
     * 
     */
    public function validate(array $messageArr) : bool
    {
        $validator = new Validator;

        // make it
        $validation = $validator->make($messageArr, [
            'data'                      => 'required',
            'data.id'                   => 'required|numeric',//updatedAt, createdAt
            'data.updatedAt'            => 'required|date:'.DateTimeInterface::RFC3339,//, createdAt
            'data.createdAt'            => 'required|date:'.DateTimeInterface::RFC3339,//updatedAt, 
            'data.slug'                 => 'required',//updatedAt, 
            'action'                    => 'required|in:'.Constant::CREATE_ACTION.','.Constant::UPDATE_ACTION.','.Constant::DELETE_ACTION,
            'metaData'                  => 'required',
            'metaData.resourceName'     => 'required',
            'metaData.groups'           => 'array',
            'metaData.groups.*'         => 'required'
        ]);

        $validation->validate();

        if ($validation->fails()) {
            // handling errors
            $errors = $validation->errors();
            //dd($errors);
            return false;
        } else {
            // validation passes
            return true;
        }

        

    }


}