<?php


namespace Ghost\Validations;


use Ghost\Exception\ValidationException;

class CarouselValidator implements Validator
{

    /**
     * @var array $errors
     */
    private array $errors;

    /**
     * ServiceValidator constructor.
     */
    public function __construct()
    {
        $this->errors = [];

    }

    public function validate(array $object, bool $is_update = false)
    {

        // Empty validation
        if (empty($object['title']))  $this->errors['title'] = 'Veuillez indiquer un titre';
        if (empty($object['cta']))  $this->errors['cta'] = 'Veuillez indiquer un titre de bouton';
        if (empty($object['link']))  $this->errors['link'] = 'Veuillez indiquer un lien';

        if(!empty($this->errors)) throw new ValidationException('Des informations sont manquantes', $this->errors);

    }

    public function validateConstraint(array $object, bool $is_update = false, ?int $product_id = null)
    {

    }
}